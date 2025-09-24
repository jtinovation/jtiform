<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    // 🔹 Tampilkan list pertanyaan di form tertentu
    public function indexQuestion(Request $request, $formId)
    {
        $form = Form::findOrFail($formId);
        $search = $request->query('search-input');

        $query = Question::with('options')
            ->where('m_form_id', $formId)
            ->orderBy('sequence', 'asc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        $questions = $query->paginate(10)->withQueryString();

        return view('content.question-layout.index', compact('form', 'questions'));
    }

    // 🔹 Tampilkan halaman tambah pertanyaan
    public function createQuestion($formId)
    {
        $form = Form::findOrFail($formId);
        return view('content.question-layout.create', compact('form'));
    }

    // 🔹 Simpan pertanyaan baru (support multiple)
    public function storeQuestion(Request $request, $formId)
    {
        $form = Form::findOrFail($formId);

        $request->validate([
            'questions'                        => 'required|array|min:1',
            'questions.*.question'             => 'required|string|max:500',
            'questions.*.type'                 => 'required|in:text,option,checkbox',
            'questions.*.sequence'             => 'required|integer|min:1',
            'questions.*.is_required'          => 'nullable|boolean',
            'questions.*.options'              => 'nullable|array'
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->questions as $q) {
                // Simpan pertanyaan utama
                $question = Question::create([
                    'm_form_id'   => $form->id,
                    'question'    => $q['question'],
                    'type'        => $q['type'],
                    'sequence'    => $q['sequence'],
                    'is_required' => isset($q['is_required']) ? 1 : 0,
                ]);

                // Simpan opsi jika tipe adalah option/checkbox
                if (in_array($q['type'], ['option', 'checkbox']) && isset($q['options'])) {
                    foreach ($q['options'] as $index => $opt) {
                        $opt = trim($opt);
                        if ($opt !== '') {
                            QuestionOption::create([
                                'm_question_id' => $question->id,
                                'answer'        => $opt,
                                'sequence'      => $index + 1,
                                'point'         => 0,
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('forms.questions.index', $formId)
                ->with('success', 'Semua pertanyaan berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan pertanyaan: ' . $e->getMessage());
        }
    }

    // 🔹 Edit pertanyaan
    public function editQuestion($formId, $questionId)
    {
        $form = Form::findOrFail($formId);
        $question = Question::with('options')
            ->where('m_form_id', $formId)
            ->findOrFail($questionId);

        return view('content.question-layout.edit', compact('form', 'question'));
    }
    // 🔹 Update pertanyaan
    public function updateQuestion(Request $request, $formId, $questionId)
    {
        $request->validate([
            'question'         => 'required|string|max:500',
            'type'             => 'required|in:text,option,checkbox',
            'sequence'         => 'required|integer|min:1',
            'is_required'      => 'nullable|boolean',
            'options_textarea' => 'nullable|string'
        ]);

        $form = Form::findOrFail($formId);
        $question = Question::where('m_form_id', $formId)->findOrFail($questionId);

        $question->update([
            'question'    => $request->question,
            'type'        => $request->type,
            'sequence'    => $request->sequence,
            'is_required' => $request->is_required ?? 0,
        ]);

        // Hapus semua opsi lama, lalu simpan yang baru jika ada
        $question->options()->delete();
        if (in_array($request->type, ['option', 'checkbox']) && $request->filled('options_textarea')) {
            $options = preg_split('/\r\n|\r|\n/', $request->options_textarea);
            foreach ($options as $index => $opt) {
                $opt = trim($opt);
                if ($opt !== '') {
                    QuestionOption::create([
                        'm_question_id' => $question->id,
                        'answer'        => $opt,
                        'sequence'      => $index + 1,
                        'point'         => 0,
                    ]);
                }
            }
        }

        return redirect()->route('forms.questions.index', $form->id)
            ->with('success', 'Pertanyaan berhasil diupdate.');
    }
    // 🔹 Hapus pertanyaan
    public function deleteQuestion($formId, $questionId)
    {
        $form = Form::findOrFail($formId);
        $question = Question::where('m_form_id', $formId)->findOrFail($questionId);

        $question->options()->delete();
        $question->delete();

        return redirect()->route('forms.questions.index', $form->id)
            ->with('success', 'Pertanyaan berhasil dihapus.');
    }
}
