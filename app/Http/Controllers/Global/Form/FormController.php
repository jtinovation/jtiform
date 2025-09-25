<?php

namespace App\Http\Controllers\Global\Form;

use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Global\Form\StoreFormRequest;
use App\Models\Answer;
use App\Models\AnswerOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Form;
use App\Models\Question;
use App\Models\Submission;
use App\Models\SubmissionTarget;
use Carbon\Carbon;

class FormController extends Controller
{
  public function index(Request $request)
  {
    $search = $request->input('search');
    $forms = Form::query()
      ->withTrashed()
      ->when($search, function ($query, $search) {
        return $query->where(function ($q) use ($search) {
          $q->where('title', 'like', "%{$search}%")
            ->orWhere('type', 'like', "%{$search}%")
            ->orWhere('code', 'like', "%{$search}%");
        });
      })
      ->orderBy('created_at', 'desc')
      ->paginate(10)
      ->withQueryString();

    return view('content.form.index', compact('forms'));
  }

  public function create()
  {
    return view('content.form.create');
  }

  public function store(StoreFormRequest $request)
  {
    $validated = $request->validated();

    if ($request->hasFile('cover')) {
      $validated['cover_file'] = FileHelper::storeFile($request->file('cover'), '/form');
      $validated['cover_path'] = '/form';
    }

    Form::create([
      'code' => $validated['code'],
      'type' => $validated['type'],
      'title' => $validated['title'],
      'description' => $validated['description'] ?? null,
      'start_at' => $validated['start_at'],
      'end_at' => $validated['end_at'],
      'cover_path' => $validated['cover_path'] ?? null,
      'cover_file' => $validated['cover_file'] ?? null,
    ]);

    return redirect()->route('form.index')->with('success', 'Form berhasil dibuat.');
  }

  public function show($id)
  {
    $form = Form::withTrashed()->findOrFail($id);
    $questions = Question::where('m_form_id', $id)
      ->with('options')
      ->orderBy('sequence')
      ->get();

    return view('content.form.show', compact('form', 'questions'));
  }

  public function edit($id)
  {
    $form = Form::withTrashed()->findOrFail($id);
    return view('content.form.edit', compact('form'));
  }

  public function update(StoreFormRequest $request, $id)
  {
    $form = Form::withTrashed()->findOrFail($id);
    $validated = $request->validated();

    if ($request->hasFile('cover')) {
      // Hapus file lama jika ada
      if ($form->cover_file && $form->cover_path) {
        FileHelper::deleteFile($form->cover_file, $form->cover_path);
      }
      $validated['cover_file'] = FileHelper::storeFile($request->file('cover'), '/form');
      $validated['cover_path'] = '/form';
    }

    $form->update([
      'code' => $validated['code'],
      'type' => $validated['type'],
      'title' => $validated['title'],
      'description' => $validated['description'] ?? null,
      'start_at' => $validated['start_at'],
      'end_at' => $validated['end_at'],
      'cover_path' => $validated['cover_path'] ?? $form->cover_path,
      'cover_file' => $validated['cover_file'] ?? $form->cover_file,
    ]);

    return redirect()->route('form.index')->with('success', 'Form berhasil diperbarui.');
  }

  public function restore($id)
  {
    $form = Form::withTrashed()->findOrFail($id);

    if ($form->trashed()) {
      $form->restore();
      return redirect()->route('form.index')->with('success', 'Form berhasil dipulihkan.');
    }

    return redirect()->route('form.index')->with('info', 'Form tidak dalam keadaan terhapus.');
  }

  public function delete($id)
  {
    $form = Form::withTrashed()->findOrFail($id);

    if ($form->trashed()) {
      $form->forceDelete();
    } else {
      $form->delete();
    }

    return response()->json(['success' => true, 'message' => 'Form berhasil dihapus.']);
  }

  public function showActiveForm(Request $request)
  {
    $search = $request->query('search-input');

    $now = Carbon::now();
    $userId = Auth::id();

    $query = Form::query();

    $query->where('is_active', true);

    $query->where(function ($q) use ($now) {
      $q->whereNull('start_at')->orWhereDate('start_at', '<=', $now->toDateString());
    })
      ->where(function ($q) use ($now) {
        $q->whereNull('end_at')->orWhereDate('end_at', '>=', $now->toDateString());
      });

    if ($search) {
      $query->where(function ($q) use ($search) {
        $q->where('title', 'like', "%{$search}%")
          ->orWhere('type', 'like', "%{$search}%");
      });
    }

    $forms = $query->latest('end_at')->paginate(10)->withQueryString();

    // Check submission status for each form - only consider as "completed" if submitted_at is not null
    $submissionStatus = [];
    foreach ($forms as $form) {
      $submissionStatus[$form->id] = Submission::where('m_form_id', $form->id)
        ->where('m_user_id', $userId)
        ->whereNotNull('submitted_at') // Hanya yang sudah submit beneran
        ->exists();
    }

    return view('content.form.form-active', compact('forms', 'submissionStatus'));
  }

  public function showFormDetailSubmit($formId)
  {
    $userId = Auth::id();

    $form = Form::findOrFail($formId);

    $questions = Question::where('m_form_id', $formId)
      ->with('options')
      ->orderBy('sequence', 'asc')
      ->get();

    $submission = Submission::where('m_form_id', $formId)
        ->where('m_user_id', $userId)
        ->first();

    if (!$submission) {
        return redirect()->back()->with('error', 'Anda belum mengerjakan form ini.');
    }

    $submissionTarget = SubmissionTarget::where('t_submission_id', $submission->id)
        ->first();

    $answers = Answer::where('t_submission_target_id', $submissionTarget->id)
      ->with('answerOptions')
      ->get();

    return view('content.form.detail-form', compact('form', 'questions', 'submission', 'submissionTarget', 'answers'));
  }

  public function fillForm($formId)
  {
    $form = Form::findOrFail($formId);
    $questions = Question::where('m_form_id', $formId)
      ->with('options')
      ->orderBy('sequence')
      ->get();

    // Cek apakah user sudah pernah submit form ini
    $existingSubmission = Submission::where('m_form_id', $formId)
      ->where('m_user_id', Auth::id())
      ->whereNotNull('submitted_at') // Hanya yang sudah submit
      ->first();

    // Bersihkan submission yang in_progress tapi sudah lama (> 2 jam)
    Submission::where('m_form_id', $formId)
      ->where('m_user_id', Auth::id())
      ->whereNull('submitted_at')
      ->where('started_at', '<', now()->subHours(2))
      ->delete();

    if (!$existingSubmission) {
      $submission = Submission::create([
        'm_form_id' => $formId,
        'm_user_id' => Auth::id(),
        'started_at' => now(),
        'submitted_at' => null, // Belum submit
        'status' => 'in_progress',
        'is_anonymous' => false,
        'is_valid' => false,
        'meta_json' => json_encode([])
      ]);

      SubmissionTarget::create([
        't_submission_id' => $submission->id,
        'target_type' => 'user',
        'target_id' => Auth::id(),
        'relation_id' => null,
        'target_label' => auth()->user()->name ?? 'Unknown User',
        'context_json' => json_encode([])
      ]);
    }

    return view('content.form.fill', compact('form', 'questions'));
  }

  public function submitForm(Request $request, $id)
  {
    $form = Form::findOrFail($id);

    $submission = Submission::where('m_form_id', $id)
      ->where('m_user_id', Auth::id())
      ->first();

    if (!$submission) {
        return redirect()->route('form.active')->with('error', 'Session form tidak ditemukan. Silakan buka form kembali.');
    }

    if ($submission->submitted_at) {
        return redirect()->route('form.active')->with('error', 'Anda sudah mengumpulkan form ini sebelumnya.');
    }

    $submission->update([
        'submitted_at' => now(),
        'status' => 'completed',
        'is_valid' => true
    ]);

    $submissionTarget = SubmissionTarget::where('t_submission_id', $submission->id)->first();
    if ($request->has('answer')) {
        $questions = Question::whereIn('id', array_keys($request->answer))
                           ->get()
                           ->keyBy('id');

        foreach ($request->answer as $questionId => $answer) {
            $question = $questions->get($questionId);

            if (!$question) continue;

            if (is_array($answer)) {
                $answerRecord = Answer::create([
                    't_submission_target_id' => $submissionTarget->id,
                    'm_question_id' => $questionId,
                    'text_value' => null,
                    'm_question_option_id' => null,
                    'score' => 0,
                    'checked_at' => now()
                ]);

                foreach ($answer as $optionId) {
                    AnswerOption::create([
                        't_answer_id' => $answerRecord->id,
                        'm_question_option_id' => $optionId
                    ]);
                }
            } else {
                if ($question->type === 'text') {
                    Answer::create([
                        't_submission_target_id' => $submissionTarget->id,
                        'm_question_id' => $questionId,
                        'text_value' => $answer,
                        'm_question_option_id' => null,
                        'score' => 0,
                        'checked_at' => now()
                    ]);
                } else {
                    Answer::create([
                        't_submission_target_id' => $submissionTarget->id,
                        'm_question_id' => $questionId,
                        'text_value' => null,
                        'm_question_option_id' => $answer,
                        'score' => 0,
                        'checked_at' => now()
                    ]);
                }
            }
        }
    }

    return redirect()->route('form.active')->with('success', 'Form berhasil dikumpulkan!');
  }
}
