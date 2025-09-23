<?php

namespace App\Http\Controllers\Global\Form;

use App\Http\Controllers\Controller;
use App\Http\Requests\Global\Form\StoreQuestionRequest;
use App\Http\Requests\Global\Form\UpdateQuestionRequest;
use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
  public function index(Request $request, $id)
  {
    $search = $request->input('search');
    $questions = Question::query()
      ->with([
        'options' => function ($query) {
          $query->orderBy('sequence', 'asc');
        }
      ])
      ->where('m_form_id', $id)
      ->when($search, function ($query, $search) {
        return $query->where('question', 'like', "%{$search}%");
      })
      ->orderBy('sequence', 'asc')
      ->paginate(10)
      ->withQueryString();
    $form = Form::withTrashed()->findOrFail($id);
    return view('content.form.question.index', compact('questions', 'form'));
  }

  public function create($id)
  {
    $form = Form::withTrashed()->findOrFail($id);
    return view('content.form.question.create', compact('form'));
  }

  public function store(StoreQuestionRequest $request, $id)
  {
    $validated = $request->validated();

    try {
      DB::beginTransaction();

      foreach ($validated['questions'] as $question) {
        $newQuestion = Question::create([
          'm_form_id' => $question['m_form_id'],
          'sequence' => $question['sequence'],
          'question' => $question['question'],
          'type' => $question['type'],
          'is_required' => $question['is_required'] ?? false,
        ]);

        if (in_array($question['type'], ['checkbox', 'option']) && isset($question['options'])) {
          foreach ($question['options'] as $option) {
            $newQuestion->options()->create([
              'sequence' => $option['sequence'],
              'answer' => $option['label'],
              'point' => $option['point'],
            ]);
          }
        }
      }

      DB::commit();
      return redirect()->route('form.question.index', ['id' => $id])->with('success', 'Pertanyaan berhasil disimpan.');
    } catch (\Exception $e) {
      DB::rollBack();
      return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan pertanyaan: ' . $e->getMessage())->withInput();
    }
  }

  public function edit($id)
  {
    $form = Form::withTrashed()->findOrFail($id);
    $questions = Question::where('m_form_id', $id)->with('options')->orderBy('sequence', 'asc')->get();
    return view('content.form.question.edit', compact('form', 'questions'));
  }

  public function update(UpdateQuestionRequest $request, $id)
  {
    $form = Form::withTrashed()->findOrFail($id);
    $validated = $request->validated();

    DB::beginTransaction();
    try {
      $existingQuestions = Question::where('m_form_id', $id)
        ->with('options')
        ->get()
        ->keyBy('id'); // key: question_id

      $keptQuestionIds = [];

      foreach ($validated['questions'] as $qData) {
        $question = null;

        $isRequired = !empty($qData['is_required']) ? 1 : 0;

        if (!empty($qData['id'])) {
          $questionId = $qData['id'];
          /** @var Question $question */
          $question = $existingQuestions->get($questionId);
          if (!$question) {
            throw new \RuntimeException("Pertanyaan dengan ID {$questionId} tidak ditemukan.");
          }

          $question->fill([
            'm_form_id'   => $id,
            'sequence'    => (int) $qData['sequence'],
            'question'    => $qData['question'],
            'type'        => $qData['type'],
            'is_required' => $isRequired,
          ])->save();
        } else {
          $question = Question::create([
            'm_form_id'   => $id,
            'sequence'    => (int) $qData['sequence'],
            'question'    => $qData['question'],
            'type'        => $qData['type'],
            'is_required' => $isRequired,
          ]);
        }

        $keptQuestionIds[] = $question->id;

        $type = $qData['type'];
        if (in_array($type, ['checkbox', 'option'])) {
          $optionsData = collect($qData['options'] ?? []);

          $existingOptions = $question->options()->get()->keyBy('id');

          $keptOptionIdsForQuestion = [];

          foreach ($optionsData as $opt) {
            $seq   = (int) ($opt['sequence'] ?? 0);
            $label = (string) ($opt['label'] ?? $opt['answer'] ?? '');
            $point = (int) ($opt['point'] ?? 0);

            if (!empty($opt['id'])) {
              $optId = $opt['id'];
              $optModel = $existingOptions->get($optId);

              if (!$optModel) {
                throw new \RuntimeException("Opsi dengan ID {$optId} tidak ditemukan untuk pertanyaan {$question->id}.");
              }

              $optModel->fill([
                'sequence' => $seq,
                'answer'   => $label, // map label -> answer
                'point'    => $point,
              ])->save();

              $keptOptionIdsForQuestion[] = $optModel->id;
            } else {
              $newOpt = $question->options()->create([
                'sequence' => $seq,
                'answer'   => $label,
                'point'    => $point,
              ]);
              $keptOptionIdsForQuestion[] = $newOpt->id;
            }
          }

          if (!empty($keptOptionIdsForQuestion)) {
            $question->options()
              ->whereNotIn('id', $keptOptionIdsForQuestion)
              ->delete();
          } else {
            $question->options()->delete();
          }
        } else {
          $question->options()->delete();
        }
      }

      $removedQuestionIds = collect($validated['removed_question_ids'] ?? [])->filter()->values();
      if ($removedQuestionIds->isNotEmpty()) {
        Question::where('m_form_id', $id)
          ->whereIn('id', $removedQuestionIds)
          ->delete();
      }

      $removedOptionIds = collect($validated['removed_option_ids'] ?? [])->filter()->values();
      if ($removedOptionIds->isNotEmpty()) {
        QuestionOption::whereIn('id', $removedOptionIds)->delete();
      }

      if (!empty($keptQuestionIds)) {
        Question::where('m_form_id', $id)
          ->whereNotIn('id', $keptQuestionIds)
          ->delete();
      }

      DB::commit();
      return redirect()
        ->route('form.question.index', ['id' => $id])
        ->with('success', 'Pertanyaan berhasil diperbarui.');
    } catch (\Throwable $e) {
      DB::rollBack();
      return back()
        ->with('error', 'Gagal memperbarui pertanyaan: ' . $e->getMessage())
        ->withInput();
    }
  }
}
