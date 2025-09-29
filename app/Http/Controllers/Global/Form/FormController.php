<?php

namespace App\Http\Controllers\Global\Form;

use App\Enums\FormRespondentTypeEnum;
use App\Helpers\ApiHelper;
use App\Helpers\FileHelper;
use App\Helpers\FormHelper;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FormController extends Controller
{
  protected $apiHelper;
  protected $formHelper;

  public function __construct(ApiHelper $apiHelper, FormHelper $formHelper)
  {
    $this->apiHelper = $apiHelper;
    $this->formHelper = $formHelper;
  }

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

    $respondents = [
      'type' => $validated['responden_type'],
    ];

    switch ($validated['responden_type']) {
      case FormRespondentTypeEnum::ALL->value:
        break;

      case FormRespondentTypeEnum::MAJOR->value:
        $respondents['major_id'] = $validated['major_id'];
        break;

      case FormRespondentTypeEnum::STUDY_PROGRAM->value:
        $respondents['major_id'] = $validated['major_id'];
        $respondents['study_program_id'] = $validated['study_program_id'];
        break;

      default:
        $hasMajor = isset($validated['major_id']) && $validated['major_id'];
        $hasStudy = isset($validated['study_program_id']);
        $hasIds   = isset($validated['respondent_ids']);

        if ($hasIds && $hasStudy && $hasMajor) {
          $respondents += [
            'major_id'         => $validated['major_id'],
            'study_program_id' => $validated['study_program_id'],
            'respondent_ids'   => $validated['respondent_ids'],
          ];
        } elseif ($hasStudy && $hasMajor) {
          $respondents += [
            'major_id'         => $validated['major_id'],
            'study_program_id' => $validated['study_program_id'],
          ];
        } elseif ($hasMajor) {
          $respondents['major_id'] = $validated['major_id'];
        } else {
          $respondents['respondent_ids'] = $validated['respondent_ids'];
        }
        break;
    }


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
      'respondents' => $respondents
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

  public function active(Request $request)
  {
    $search = $request->query('search');

    $user = $this->apiHelper->getMe(Auth::user()->token);

    $forms = $this->formHelper->getFormVisibleToUser($user, $search, false);

    return view('content.form.active', compact('forms'));
  }

  public function history(Request $request)
  {
    $search = $request->query('search');

    $submissions = Submission::where('m_user_id', Auth::user()->id)
      ->with('form')
      ->when($search, function ($query, $search) {
        return $query->whereHas('form', function ($q) use ($search) {
          $q->where('title', 'like', "%{$search}%")
            ->orWhere('code', 'like', "%{$search}%");
        });
      })
      ->orderBy('created_at', 'desc')
      ->paginate(10)
      ->withQueryString();

    return view('content.form.history', compact('submissions'));
  }

  public function showFormDetailSubmit($formId)
  {
    $userId = Auth::user()->id;

    $form = Form::findOrFail($formId);

    $questions = Question::where('m_form_id', $formId)
      ->with(['options' => function ($query) {
        $query->orderBy('sequence', 'asc');
      }])
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

  public function fill($formId)
  {
    $form = Form::findOrFail($formId);
    $questions = Question::where('m_form_id', $formId)
      ->with([
        'options' => function ($query) {
          $query->orderBy('sequence');
        }
      ])
      ->orderBy('sequence')
      ->get();

    return view('content.form.fill', compact('form', 'questions'));
  }

  public function submit(Request $request, $formId)
  {
    // Ambil form + pertanyaan + opsi untuk validasi & mapping
    $form = Form::query()
      ->with(['questions' => function ($q) {
        $q->select('id', 'm_form_id', 'type', 'question', 'is_required');
      }, 'questions.options' => function ($q) {
        $q->select('id', 'm_question_id', 'answer', 'sequence', 'point');
      }])
      ->findOrFail($formId);

    $answersInput = $request->input('answer', []); // array: [questionId => value|[values]]

    // ---- VALIDASI SERVER-SIDE UNTUK PERTANYAAN WAJIB ----
    $errors = [];

    foreach ($form->questions as $question) {
      $qid = $question->id;
      $type = $question->type; // 'text' | 'checkbox' | 'option'
      $required = (bool)($question->is_required ?? false);

      // Ambil jawaban user (bisa string, bisa array)
      $value = $answersInput[$qid] ?? null;

      if ($required) {
        if ($type === 'text') {
          if (!is_string($value) || trim($value) === '') {
            $errors["answer.$qid"] = 'Pertanyaan wajib diisi.';
          }
        } elseif ($type === 'option') {
          if (empty($value)) {
            $errors["answer.$qid"] = 'Pilih salah satu opsi.';
          }
        } elseif ($type === 'checkbox') {
          if (!is_array($value) || count(array_filter($value)) === 0) {
            $errors["answer.$qid"] = 'Pilih minimal satu opsi.';
          }
        }
      }

      // Untuk pilihan (radio/checkbox), pastikan opsi yang dikirim memang milik pertanyaan tsb
      if (($type === 'option' && !empty($value)) || ($type === 'checkbox' && is_array($value) && !empty($value))) {
        $validOptionIds = $question->options->pluck('id')->all();

        if ($type === 'option') {
          if (!in_array($value, $validOptionIds, true)) {
            $errors["answer.$qid"] = 'Opsi tidak valid.';
          }
        } else { // checkbox
          $picked = array_values(array_unique(array_filter((array)$value)));
          $diff = array_diff($picked, $validOptionIds);
          if (!empty($diff)) {
            $errors["answer.$qid"] = 'Terdapat opsi tidak valid.';
          }
        }
      }
    }

    if (!empty($errors)) {
      throw ValidationException::withMessages($errors);
    }

    // ---- SIMPAN JAWABAN ----
    try {
      DB::beginTransaction();

      $submission = Submission::create([
        'm_form_id'    => $form->id,
        'm_user_id'    => Auth::id(),
        'submitted_at' => now(),
        'is_valid'     => true, // sudah divalidasi di atas
      ]);

      // NOTE: sementara target_type 'general' seperti contohmu
      $submissionTarget = SubmissionTarget::create([
        't_submission_id' => $submission->id,
        'target_type'     => 'general',
        'target_id'       => null,
      ]);

      $now = now();

      foreach ($form->questions as $question) {
        $qid = $question->id;
        $type = $question->type;

        // Jika optional & tidak diisi, lewati
        if (!array_key_exists($qid, $answersInput)) {
          continue;
        }

        // text
        if ($type === 'text') {
          $text = (string)($answersInput[$qid] ?? '');
          $text = trim($text);

          // Kalau kosong dan tidak required, skip
          if ($text === '' && !$question->is_required) {
            continue;
          }

          Answer::create([
            't_submission_target_id' => $submissionTarget->id,
            'm_question_id'          => $qid,
            'text_value'             => $text,
            'm_question_option_id'   => null,
            'score'                  => 0, // text tidak dinilai
            'checked_at'             => $now,
          ]);

          continue;
        }

        // radio/option (single)
        if ($type === 'option') {
          $optionId = $answersInput[$qid] ?? null;
          if (!$optionId) {
            // optional & tidak diisi → skip
            if (!$question->is_required) {
              continue;
            }
          }

          // Ambil point
          $opt = $question->options->firstWhere('id', $optionId);
          $point = $opt?->point ?? 0;

          Answer::create([
            't_submission_target_id' => $submissionTarget->id,
            'm_question_id'          => $qid,
            'text_value'             => null,
            'm_question_option_id'   => $optionId,
            'score'                  => $point,
            'checked_at'             => $now,
          ]);

          continue;
        }

        // checkbox (multiple)
        if ($type === 'checkbox') {
          $picked = array_values(array_unique(array_filter((array)($answersInput[$qid] ?? []))));

          if (empty($picked)) {
            // optional & tidak diisi → skip
            if (!$question->is_required) {
              continue;
            }
          }

          // Hitung total point dari semua opsi terpilih
          $optPoints = $question->options
            ->whereIn('id', $picked)
            ->pluck('point')
            ->all();

          $totalPoint = array_sum($optPoints);

          // Buat satu baris t_answer, detail opsi disimpan di t_answer_option
          $answer = Answer::create([
            't_submission_target_id' => $submissionTarget->id,
            'm_question_id'          => $qid,
            'text_value'             => null,
            'm_question_option_id'   => null, // pakai detail di t_answer_option
            'score'                  => $totalPoint,
            'checked_at'             => $now,
          ]);

          // Insert t_answer_option untuk setiap pilihan
          $rows = [];
          $ts = now();
          foreach ($picked as $oid) {
            $rows[] = [
              'id'                 => Str::uuid()->toString(),
              't_answer_id'        => $answer->id,
              'm_question_option_id' => $oid,
              'created_at'         => $ts,
              'updated_at'         => $ts,
            ];
          }
          if (!empty($rows)) {
            AnswerOption::insert($rows);
          }
        }
      }

      DB::commit();

      return redirect()
        ->route('form.history', $form->id)
        ->with('success', 'Jawaban berhasil dikirim. Terima kasih!');
    } catch (\Throwable $e) {
      DB::rollBack();
      dd($e);
      report($e);

      return back()
        ->withInput()
        ->with('error', 'Terjadi kesalahan saat menyimpan jawaban. Silakan coba lagi.');
    }
  }
}
