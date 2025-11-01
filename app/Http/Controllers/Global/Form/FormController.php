<?php

namespace App\Http\Controllers\Global\Form;

use App\Enums\FormTypeEnum;
use App\Helpers\ApiHelper;
use App\Helpers\FileHelper;
use App\Helpers\FormHelper;
use App\Helpers\SemesterApiHelper;
use App\Helpers\SubjectLectureApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Global\Form\CloneFormRequest;
use App\Http\Requests\Global\Form\StoreFormRequest;
use App\Http\Requests\Global\Form\UpdateFormRequest;
use App\Jobs\Report\GenerateEvaluationReportJob;
use App\Models\Answer;
use App\Models\AnswerOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Form;
use App\Models\Question;
use App\Models\Submission;
use App\Models\SubmissionTarget;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FormController extends Controller
{
  protected $apiHelper;
  protected $formHelper;
  protected $subjectLectureApiHelper;
  protected $semesterApiHelper;

  public function __construct(
    ApiHelper $apiHelper,
    FormHelper $formHelper,
    SubjectLectureApiHelper $subjectLectureApiHelper,
    SemesterApiHelper $semesterApiHelper
  ) {
    $this->apiHelper = $apiHelper;
    $this->formHelper = $formHelper;
    $this->subjectLectureApiHelper = $subjectLectureApiHelper;
    $this->semesterApiHelper = $semesterApiHelper;
  }

  public function index(Request $request)
  {
    $search = $request->input('search');

    $ownerId = null;
    $majorId = null;
    $studyProgramId = null;
    $user = Auth::user();
    if ($user->hasAnyRole('kajur|kaprodi')) {
      $ownerId = $user->id;
      $majorId = $user->major_id;
      $studyProgramId = $user->study_program_id;
    }

    $forms = Form::query()
      ->withTrashed()
      ->with('creator:id,name')
      ->when($search, function ($query, $search) {
        return $query->where(function ($q) use ($search) {
          $q->where('title', 'like', "%{$search}%")
            ->orWhere('type', 'like', "%{$search}%")
            ->orWhere('code', 'like', "%{$search}%")
            ->orWhereHas('creator', function ($q2) use ($search) {
              $q2->where('name', 'like', "%{$search}%");
            });
        });
      })
      ->when($ownerId, function ($query) use ($ownerId, $majorId, $studyProgramId, $user) {
        return $query->where(function ($q) use ($ownerId, $majorId) {
          $q->where('created_by', $ownerId)
            ->orWhereJsonContains('respondents->major_id', $majorId);
        })
          ->when($user->hasAnyRole('kaprodi'), function ($query) use ($studyProgramId) {
            return $query->whereJsonContains('respondents->study_program_id', $studyProgramId);
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

    $respondents = FormHelper::getRespondentIds($validated);

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
      'respondents' => $respondents,
      'created_by' => Auth::user()->id,
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

  public function update(UpdateFormRequest $request, $id)
  {
    $form = Form::withTrashed()->findOrFail($id);
    $validated = $request->validated();

    $respondents = FormHelper::getRespondentIds($validated);

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
      'respondents' => $respondents
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
    $submissions = FormHelper::formHistory($request);

    return view('content.form.history', compact('submissions'));
  }

  public function showFormDetailSubmit($formId)
  {
    $userId = Auth::user()->id;

    $form = Form::findOrFail($formId);

    if ($form->type === FormTypeEnum::LECTURE_EVALUATION->value) {
      return redirect()->route('form.result.evaluation', ['id' => $formId]);
    }

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

    if ($form->type === FormTypeEnum::GENERAL->value) {
      $questions = Question::where('m_form_id', $formId)
        ->with([
          'options' => function ($query) {
            $query->orderBy('sequence');
          }
        ])
        ->orderBy('sequence')
        ->get();
      return view('content.form.fill', compact('form', 'questions'));
    } else {

      $user = $this->apiHelper->getMe(Auth::user()->token);

      $activeSemester = collect(Arr::get($user, 'student_detail.student_semester', []))
        ->first(function ($item) {
          return Arr::get($item, 'is_active') === true;
        });

      $lectures = $this->apiHelper->GetLectureOnSubject(
        Auth::user()->token,
        Arr::get($user, 'student_detail.m_study_program_id'),
        Arr::get($activeSemester, 'semester_id')
      );

      $studyProgramName = Arr::get($user, 'student_detail.study_program_name', 'N/A');
      $key = "form:choose-lecture:{" . Auth::user()->id . "}:{$formId}";
      $prefill = [];
      if ($cached = Redis::get($key)) {
        $prefill = json_decode($cached, true) ?: [];
      }

      return view('content.form.choose-lecture', compact('form', 'lectures', 'studyProgramName', 'prefill'));
    }
  }

  public function submit(Request $request, $formId)
  {
    $form = Form::query()
      ->with(['questions' => function ($q) {
        $q->select('id', 'm_form_id', 'type', 'question', 'is_required');
      }, 'questions.options' => function ($q) {
        $q->select('id', 'm_question_id', 'answer', 'sequence', 'point');
      }])
      ->findOrFail($formId);

    $answersInput = $request->input('answer', []);

    $errors = [];

    foreach ($form->questions as $question) {
      $qid = $question->id;
      $type = $question->type;
      $required = (bool)($question->is_required ?? false);

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

      if (($type === 'option' && !empty($value)) || ($type === 'checkbox' && is_array($value) && !empty($value))) {
        $validOptionIds = $question->options->pluck('id')->all();

        if ($type === 'option') {
          if (!in_array($value, $validOptionIds, true)) {
            $errors["answer.$qid"] = 'Opsi tidak valid.';
          }
        } else {
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

    try {
      DB::beginTransaction();

      $submission = Submission::create([
        'm_form_id'    => $form->id,
        'm_user_id'    => Auth::id(),
        'submitted_at' => now(),
        'is_valid'     => true,
      ]);

      $submissionTarget = SubmissionTarget::create([
        't_submission_id' => $submission->id,
        'target_type'     => 'general',
        'target_id'       => null,
      ]);

      $now = now();

      foreach ($form->questions as $question) {
        $qid = $question->id;
        $type = $question->type;

        if (!array_key_exists($qid, $answersInput)) {
          continue;
        }

        if ($type === 'text') {
          $text = (string)($answersInput[$qid] ?? '');
          $text = trim($text);

          if ($text === '' && !$question->is_required) {
            continue;
          }

          Answer::create([
            't_submission_target_id' => $submissionTarget->id,
            'm_question_id'          => $qid,
            'text_value'             => $text,
            'm_question_option_id'   => null,
            'score'                  => 0,
            'checked_at'             => $now,
          ]);

          continue;
        }

        if ($type === 'option') {
          $optionId = $answersInput[$qid] ?? null;
          if (!$optionId) {
            if (!$question->is_required) {
              continue;
            }
          }

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

        if ($type === 'checkbox') {
          $picked = array_values(array_unique(array_filter((array)($answersInput[$qid] ?? []))));

          if (empty($picked)) {
            if (!$question->is_required) {
              continue;
            }
          }

          $optPoints = $question->options
            ->whereIn('id', $picked)
            ->pluck('point')
            ->all();

          $totalPoint = array_sum($optPoints);

          $answer = Answer::create([
            't_submission_target_id' => $submissionTarget->id,
            'm_question_id'          => $qid,
            'text_value'             => null,
            'm_question_option_id'   => null,
            'score'                  => $totalPoint,
            'checked_at'             => $now,
          ]);

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
      report($e);

      return back()
        ->withInput()
        ->with('error', 'Terjadi kesalahan saat menyimpan jawaban. Silakan coba lagi.');
    }
  }

  public function storeChosenLectures(Request $request, $formId)
  {
    $request->validate([
      'selections' => 'required|array',         // selections[subject_id] = [lecture_id, ...]
    ]);

    // Validasi server: tiap subject minimal 1 dosen
    foreach ($request->input('selections') as $subjectId => $lectureIds) {
      if (!is_array($lectureIds) || count($lectureIds) < 1) {
        return back()->with('error', 'Setiap mata kuliah harus dipilihkan minimal satu dosen.')->withInput();
      }
    }

    // Simpan sementara di Redis (24 jam)
    $key = "form:choose-lecture:{" . Auth::user()->id . "}:{$formId}";
    Redis::setex($key, 60 * 60 * 24, json_encode($request->input('selections')));

    return redirect()
      ->route('form.fill.lecture', $formId) // atau ke langkah berikutnya
      ->with('success', 'Pilihan dosen berhasil disimpan sementara.');
  }

  public function fillLecture($formId)
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

    $key = "form:choose-lecture:{" . Auth::user()->id . "}:{$formId}";
    $choosenLectures = [];
    if ($cached = Redis::get($key)) {
      $choosenLectures = json_decode($cached, true) ?: [];
    }

    $user = $this->apiHelper->getMe(Auth::user()->token);

    $activeSemester = collect(Arr::get($user, 'student_detail.student_semester', []))
      ->first(function ($item) {
        return Arr::get($item, 'is_active') === true;
      });

    $lectures = $this->apiHelper->GetLectureOnSubject(
      Auth::user()->token,
      Arr::get($user, 'student_detail.m_study_program_id'),
      Arr::get($activeSemester, 'semester_id')
    );

    $selectedLecturesWithDetail = [];

    foreach ($lectures as $subjectData) {
      $subjectId = $subjectData['subject']['id'];
      if (isset($choosenLectures[$subjectId])) {
        $selectedIds = $choosenLectures[$subjectId];

        $filteredLecturers = array_filter(
          $subjectData['lectures'],
          function ($lecturer) use ($selectedIds) {
            return in_array($lecturer['id'], $selectedIds);
          }
        );

        $subjectResult = $subjectData;
        $subjectResult['lectures'] = array_values($filteredLecturers);
        $selectedLecturesWithDetail[] = $subjectResult;
      }
    }

    return view('content.form.fill-evaluation', compact('form', 'questions', 'selectedLecturesWithDetail'));
  }

  public function submitEvaluation(Request $request, $formId)
  {
    $form = Form::with(['questions' => function ($q) {
      $q->orderBy('sequence')->with(['options' => function ($o) {
        $o->orderBy('sequence');
      }]);
    }])->findOrFail($formId);

    // Payload dari form (Blade sebelumnya)
    $answersInput = $request->input('answers', []); // answers[subject_id][subject_lecture_id][question_id] = ...
    $targets      = $request->input('targets', []); // targets[subject_id][] = subject_lecture_id

    if (empty($answersInput) || empty($targets)) {
      return back()->withErrors(['answers' => 'Tidak ada jawaban atau target yang dikirim.'])->withInput();
    }

    // 1) Ambil pilihan dari Redis (ISINYA lecturer_id per subject)
    $redisKey = "form:choose-lecture:{" . Auth::id() . "}:{$formId}";
    $chosen = [];
    if ($cached = Redis::get($redisKey)) {
      $chosen = json_decode($cached, true) ?: [];
    }

    // 2) Bangun peta allowed subject_lecture_id dari API
    $user = $this->apiHelper->getMe(Auth::user()->token);
    $activeSemester = collect(Arr::get($user, 'student_detail.student_semester', []))
      ->first(fn($item) => Arr::get($item, 'is_active') === true);

    $lectures = $this->apiHelper->GetLectureOnSubject(
      Auth::user()->token,
      Arr::get($user, 'student_detail.m_study_program_id'),
      Arr::get($activeSemester, 'semester_id')
    );

    // subject_id => [allowed subject_lecture_id ...] (diturunkan dari lecturer_id yang tersimpan di Redis)
    $allowedBySubject = [];
    foreach ($lectures as $subjectData) {
      $subjectId = Arr::get($subjectData, 'subject.id');
      if (!$subjectId) continue;

      $allowedLecturerIds = (array) Arr::get($chosen, $subjectId, []);
      if (empty($allowedLecturerIds)) continue;

      foreach ((array) Arr::get($subjectData, 'lectures', []) as $lec) {
        $lecId = Arr::get($lec, 'id'); // lecturer_id
        $slid  = Arr::get($lec, 'subject_lecture_id') ?? ($lecId . '_' . $subjectId); // fallback
        if (in_array($lecId, $allowedLecturerIds, true)) {
          $allowedBySubject[$subjectId][] = $slid;
        }
      }
    }

    // 3) VALIDASI: target yang dikirim user harus termasuk allowed subject_lecture_id
    foreach ($targets as $subjectId => $slids) {
      $slids   = (array) $slids;
      $allowed = (array) Arr::get($allowedBySubject, $subjectId, []);
      foreach ($slids as $slid) {
        if (!in_array($slid, $allowed, true)) {
          return back()->withErrors(['targets' => 'Terdapat target yang tidak valid untuk mata kuliah terkait.'])->withInput();
        }
      }
    }

    // 4) VALIDASI: pertanyaan wajib per target (radio/text/checkbox)
    // Siapkan index opsi per pertanyaan untuk validasi id & ambil point
    $optionsIndex = [];
    foreach ($form->questions as $q) {
      $optionsIndex[$q->id] = $q->options->keyBy('id'); // id => Option model (punya kolom 'point')
    }

    $errors = [];
    foreach ($targets as $subjectId => $slids) {
      foreach ((array) $slids as $slid) {
        foreach ($form->questions as $q) {
          if (!$q->is_required) continue;

          $val = Arr::get($answersInput, "{$subjectId}.{$slid}.{$q->id}");

          if ($q->type === 'text') {
            if (!is_string($val) || trim($val) === '') {
              $errors["answers.{$subjectId}.{$slid}.{$q->id}"] = 'Pertanyaan wajib (teks) belum diisi.';
            }
          } elseif ($q->type === 'option') {
            $optId = $val;
            if (!$optId || !isset($optionsIndex[$q->id][$optId])) {
              $errors["answers.{$subjectId}.{$slid}.{$q->id}"] = 'Pilih salah satu opsi untuk pertanyaan wajib.';
            }
          } elseif ($q->type === 'checkbox') {
            $picked = array_values(array_unique(array_filter((array) $val)));
            if (empty($picked)) {
              $errors["answers.{$subjectId}.{$slid}.{$q->id}"] = 'Pilih minimal satu opsi untuk pertanyaan wajib.';
            } else {
              foreach ($picked as $oid) {
                if (!isset($optionsIndex[$q->id][$oid])) {
                  $errors["answers.{$subjectId}.{$slid}.{$q->id}"] = 'Terdapat opsi tidak valid.';
                  break;
                }
              }
            }
          }
        }
      }
    }

    if (!empty($errors)) {
      return back()->withErrors($errors)->withInput();
    }

    // 5) SIMPAN (mengikuti pola "general" milikmu, tapi multi-target)
    try {
      DB::beginTransaction();

      $submission = Submission::create([
        'm_form_id'    => $form->id,
        'm_user_id'    => Auth::id(),
        'submitted_at' => now(),
        'is_valid'     => true,
      ]);

      $now = now();

      foreach ($targets as $subjectId => $slids) {
        foreach ((array) $slids as $slid) {
          // tiap subject_lecture_id = satu target
          $submissionTarget = SubmissionTarget::create([
            't_submission_id' => $submission->id,
            'target_type'     => 'subject_lecture',
            'target_id'       => $slid,
          ]);

          // simpan semua pertanyaan untuk target ini
          foreach ($form->questions as $question) {
            $qid  = $question->id;
            $type = $question->type;

            if (!array_key_exists($qid, Arr::get($answersInput, "{$subjectId}.{$slid}", []))) {
              // skip jika tidak ada input & tidak required
              if (!$question->is_required) {
                continue;
              }
            }

            if ($type === 'text') {
              $text = (string) (Arr::get($answersInput, "{$subjectId}.{$slid}.{$qid}") ?? '');
              $text = trim($text);

              if ($text === '' && !$question->is_required) {
                continue;
              }

              Answer::create([
                't_submission_target_id' => $submissionTarget->id,
                'm_question_id'          => $qid,
                'text_value'             => $text,
                'm_question_option_id'   => null,
                'score'                  => 0,
                'checked_at'             => $now,
              ]);
              continue;
            }

            if ($type === 'option') {
              $optionId = Arr::get($answersInput, "{$subjectId}.{$slid}.{$qid}");
              if (!$optionId) {
                if (!$question->is_required) continue;
              }

              // validasi aman karena sudah diverifikasi di atas
              $point = optional($optionsIndex[$qid][$optionId] ?? null)->point ?? 0;

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

            if ($type === 'checkbox') {
              $picked = array_values(array_unique(array_filter(
                (array) Arr::get($answersInput, "{$subjectId}.{$slid}.{$qid}", [])
              )));

              if (empty($picked)) {
                if (!$question->is_required) continue;
              }

              // hitung total score checkbox
              $totalPoint = 0;
              foreach ($picked as $oid) {
                $totalPoint += optional($optionsIndex[$qid][$oid] ?? null)->point ?? 0;
              }

              $answer = Answer::create([
                't_submission_target_id' => $submissionTarget->id,
                'm_question_id'          => $qid,
                'text_value'             => null,
                'm_question_option_id'   => null,
                'score'                  => $totalPoint,
                'checked_at'             => $now,
              ]);

              if (!empty($picked)) {
                $ts = now();
                $rows = [];
                foreach ($picked as $oid) {
                  $rows[] = [
                    'id'                   => Str::uuid()->toString(),
                    't_answer_id'          => $answer->id,
                    'm_question_option_id' => $oid,
                    'created_at'           => $ts,
                    'updated_at'           => $ts,
                  ];
                }
                AnswerOption::insert($rows);
              }
            }
          } // end foreach questions
        } // end foreach slids
      } // end foreach subjects

      DB::commit();

      // bersihkan cache pilihan
      Redis::del($redisKey);

      return redirect()
        ->route('form.history', $form->id) // samakan dengan flow "general" milikmu
        ->with('success', 'Jawaban berhasil dikirim. Terima kasih!');
    } catch (\Throwable $e) {
      DB::rollBack();
      report($e);

      return back()
        ->withInput()
        ->with('error', 'Terjadi kesalahan saat menyimpan jawaban. Silakan coba lagi.');
    }
  }

  public function showEvaluationResult($formId)
  {
    $user = Auth::user();
    $form = Form::findOrFail($formId);

    // if ($form->type !== FormTypeEnum::GENERAL->value) {
    //   return redirect()->route('form.result', ['id' => $formId]);
    // }

    // Ambil pertanyaan + opsi untuk render
    $questions = Question::where('m_form_id', $formId)
      ->with(['options' => fn($q) => $q->orderBy('sequence')])
      ->orderBy('sequence')
      ->get();

    // Submission user untuk form ini
    $submission = Submission::where('m_form_id', $formId)
      ->where('m_user_id', $user->id)
      ->first();

    if (!$submission) {
      return redirect()->back()->with('error', 'Anda belum mengerjakan form ini.');
    }

    // Ambil semua target subject_lecture pada submission ini
    $targets = SubmissionTarget::where('t_submission_id', $submission->id)
      ->where('target_type', 'subject_lecture')
      ->get();

    if ($targets->isEmpty()) {
      // fallback: mungkin ini bukan tipe evaluasi dosen
      return redirect()->back()->with('error', 'Tidak ada target evaluasi dosen pada form ini.');
    }

    // Map: submission_target_id => jawaban (with answerOptions)
    $answersByTarget = Answer::with('answerOptions')
      ->whereIn('t_submission_target_id', $targets->pluck('id'))
      ->get()
      ->groupBy('t_submission_target_id');

    /**
     * Kita butuh nama matkul & nama dosen dari subject_lecture_id.
     * Ambil dari API yang sama seperti saat pengisian.
     */
    $me = $this->apiHelper->getMe($user->token);
    $activeSemester = collect(Arr::get($me, 'student_detail.student_semester', []))
      ->first(fn($item) => Arr::get($item, 'is_active') === true);

    $apiLectures = $this->apiHelper->GetLectureOnSubject(
      $user->token,
      Arr::get($me, 'student_detail.m_study_program_id'),
      Arr::get($activeSemester, 'semester_id')
    );

    // Build map: subject_lecture_id -> meta (subject_id/name/code, lecturer_name)
    $slMeta = [];                 // slid => ['subject_id','subject_name','subject_code','lecturer_name']
    $subjectMeta = [];            // subject_id => ['id','name','code']
    foreach ($apiLectures as $s) {
      $sid   = Arr::get($s, 'subject.id');
      $sname = Arr::get($s, 'subject.name');
      $scode = Arr::get($s, 'subject.code');
      if ($sid) {
        $subjectMeta[$sid] = ['id' => $sid, 'name' => $sname, 'code' => $scode];
        foreach ((array) Arr::get($s, 'lectures', []) as $lec) {
          $lecName = Arr::get($lec, 'user.name', 'Nama Dosen');
          $lecId   = Arr::get($lec, 'id');
          $slid    = Arr::get($lec, 'subject_lecture_id') ?? ($lecId . '_' . $sid);
          $slMeta[$slid] = [
            'subject_id'   => $sid,
            'subject_name' => $sname,
            'subject_code' => $scode,
            'lecturer_name' => $lecName,
          ];
        }
      }
    }

    /**
     * Bentuk struktur untuk view:
     * $subjectsView = [
     *   [
     *     'subject' => ['id','name','code'],
     *     'lectures' => [
     *       [
     *         'subject_lecture_id' => '...',
     *         'lecturer_name' => '...',
     *         'answers_by_qid' => [ qid => ['type','text','sel_id','sel_ids','score'] ],
     *       ],
     *     ],
     *   ],
     * ]
     */
    $bySubject = []; // subject_id => ['subject'=>..., 'lectures'=>[...]]
    foreach ($targets as $t) {
      $slid = $t->target_id;
      $meta = $slMeta[$slid] ?? null;

      // Jika meta tidak ketemu (misal data API berubah), kasih placeholder
      $sid     = $meta['subject_id']   ?? 'unknown_' . $slid;
      $sname   = $meta['subject_name'] ?? 'Mata Kuliah (tidak tersedia)';
      $scode   = $meta['subject_code'] ?? '—';
      $lecName = $meta['lecturer_name'] ?? 'Dosen (tidak tersedia)';

      if (!isset($bySubject[$sid])) {
        $bySubject[$sid] = [
          'subject'  => ['id' => $sid, 'name' => $sname, 'code' => $scode],
          'lectures' => [],
        ];
      }

      // Kumpulkan jawaban per-pertanyaan untuk target ini
      $ansCol = $answersByTarget->get($t->id, collect());
      $byQ = []; // qid => data
      foreach ($ansCol as $ans) {
        $qid = $ans->m_question_id;
        if (!isset($byQ[$qid])) {
          $byQ[$qid] = [
            'type'   => null,
            'text'   => null,
            'sel_id' => null,    // untuk radio/option
            'sel_ids' => [],      // untuk checkbox
            'score'  => (int) $ans->score,
          ];
        }
        // tipe kita ambil dari master $questions
        $byQ[$qid]['type'] = optional($questions->firstWhere('id', $qid))->type;

        if (!is_null($ans->text_value)) {
          $byQ[$qid]['text'] = $ans->text_value;
        }
        if (!is_null($ans->m_question_option_id)) {
          // radio
          $byQ[$qid]['sel_id'] = $ans->m_question_option_id;
        }
        if ($ans->relationLoaded('answerOptions') && $ans->answerOptions) {
          foreach ($ans->answerOptions as $ao) {
            $byQ[$qid]['sel_ids'][] = $ao->m_question_option_id;
          }
        }
      }
      // unikkan sel_ids
      foreach ($byQ as &$row) {
        $row['sel_ids'] = array_values(array_unique($row['sel_ids']));
      }

      $bySubject[$sid]['lectures'][] = [
        'subject_lecture_id' => $slid,
        'lecturer_name'      => $lecName,
        'answers_by_qid'     => $byQ,
      ];
    }

    // Urutkan by subject name
    $subjectsView = array_values($bySubject);
    usort($subjectsView, fn($a, $b) => strcmp($a['subject']['name'], $b['subject']['name']));

    return view('content.form.detail-evaluation', [
      'form'          => $form,
      'questions'     => $questions,
      'submission'    => $submission,
      'subjectsView'  => $subjectsView,
    ]);
  }

  public function generateReport($formId)
  {
    $form = Form::findOrFail($formId);
    $semesters = $this->semesterApiHelper->getSemesterAsOption(Auth::user()->token, $form->session_id);

    $filteredSemesters = array_filter($semesters, function ($semester) use ($form) {
      if ($form->is_even) {
        return ((int)Arr::get($semester, 'semester') % 2) === 0;
      } else {
        return ((int)Arr::get($semester, 'semester') % 2) === 1;
      }
    });

    User::where('roles', 'like', '%lecturer%')
      ->chunk(30, function ($users) use ($filteredSemesters, $form) {
        $subjectLectures =
          $this->subjectLectureApiHelper->getSubjectLectures(
            Auth::user()->token,
            [
              'user_ids' => Arr::pluck($users->toArray(), 'external_id'),
              'semester_ids' => Arr::pluck($filteredSemesters, 'id'),
            ]
          );

        dispatch(new GenerateEvaluationReportJob($subjectLectures['data'] ?? null, $form->id))->onQueue('generate-report-evaluation');
      });

    return redirect()->route('form.index')->with('success', 'Proses generate laporan telah dimulai. Mohon tunggu beberapa saat.');
  }

  public function showSummary($formId)
  {
    $form = Form::findOrFail($formId);

    // if ($form->type !== FormTypeEnum::LECTURE_EVALUATION->value) {
    //   return redirect()->route('form.result', ['id' => $formId]);
    // }

    $questions = Question::where('m_form_id', $formId)
      ->with(['options' => fn($q) => $q->orderBy('sequence')])
      ->orderBy('sequence')
      ->get();

    return view('content.form.summary', compact('form', 'questions'));
  }

  public function clone(CloneFormRequest $request, string $id)
  {
    $payload = $request->validated();

    // Ambil source beserta relasi
    $source = Form::with(['questions' => function ($q) {
      $q->orderBy('sequence', 'asc')
        ->with(['options' => function ($o) {
          $o->orderBy('sequence', 'asc');
        }]);
    }])->findOrFail($id);

    $userId = Auth::user()->id;

    $newForm = DB::transaction(function () use ($source, $payload, $userId) {
      // Clone header form (replicate semua kecuali field yang akan di-set ulang)
      $cloned = $source->replicate([
        'id',
        'code',
        'title',
        'description',
        'start_at',
        'end_at',
        'created_at',
        'updated_at',
        'deleted_at',
      ]);
      $cloned->id          = (string) Str::uuid();
      $cloned->code        = $payload['code'];
      $cloned->title       = $payload['title'];
      $cloned->description = $payload['description'] ?? $source->description;
      $cloned->start_at    = Carbon::parse($payload['start_at']);
      $cloned->end_at      = Carbon::parse($payload['end_at']);
      // “lainnya samakan dengan form yang di-clone”
      // fields seperti: type, cover_path, cover_file, respondents, session_id, is_even, is_active, created_by (opsional)
      $cloned->created_by  = $userId ?? $source->created_by;
      $cloned->is_active   = $source->is_active; // tetap sama
      $cloned->save();

      // Map ID lama -> baru (kalau suatu saat perlu)
      $questionIdMap = [];

      foreach ($source->questions as $q) {
        $newQ = $q->replicate(['id', 'm_form_id', 'created_at', 'updated_at']);
        $newQ->id        = (string) Str::uuid();
        $newQ->m_form_id = $cloned->id;
        $newQ->save();

        $questionIdMap[$q->id] = $newQ->id;

        // Clone options hanya untuk tipe checkbox & option
        if (in_array($q->type, ['checkbox', 'option'], true)) {
          foreach ($q->options as $opt) {
            $newOpt = $opt->replicate(['id', 'm_question_id', 'created_at', 'updated_at']);
            $newOpt->id            = (string) Str::uuid();
            $newOpt->m_question_id = $newQ->id;
            $newOpt->save();
          }
        }
      }

      return $cloned;
    });

    return redirect()
      ->route('form.show', $newForm->id)
      ->with('success', 'Form berhasil di-clone.');
  }
}
