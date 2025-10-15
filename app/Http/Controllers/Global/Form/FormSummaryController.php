<?php

namespace App\Http\Controllers\Global\Form;

use App\Helpers\FormHelper;
use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Submission;
use App\Models\SubmissionTarget;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class FormSummaryController extends Controller
{
  public function kpi(Request $req, string $id)
  {
    // filter
    [$from, $to] = FormHelper::dateRange($req);

    $q = Submission::query()
      ->where('m_form_id', $id)
      ->when($from, fn($qq) => $qq->where('submitted_at', '>=', $from))
      ->when($to,   fn($qq) => $qq->where('submitted_at', '<=', $to))
      ->where('is_valid', true);

    $total   = (clone $q)->count();
    $lastAt  = (clone $q)->max('submitted_at');

    // Response rate opsional: kalau kamu punya tabel undangan/target total
    // $invited = ...; // ambil dari t_submission_target unik misalnya
    $rate = null; // set persen jika ada pembagi

    return response()->json([
      'total'  => $total,
      'rate'   => $rate, // null atau angka float
      'last_at' => $lastAt ? Carbon::parse($lastAt)->format('Y-m-d H:i:s') : null,
    ]);
  }

  public function respondents(Request $req, string $id)
  {
    [$from, $to] = FormHelper::dateRange($req);
    $qSearch = trim((string) $req->get('q', ''));

    $rows = Submission::query()
      ->select([
        't_submission.id',
        't_submission.submitted_at',
        't_submission.is_valid',
        'm_user.name as name',
        // DB::raw("COALESCE(m_user.role_label, '-') as role"), // kalau tidak ada kolom ini, ganti jadi '-' atau join role
        // DB::raw("COALESCE(m_user.study_program_name, '-') as study_program"), // fallback; sesuaikan skemamu
      ])
      ->leftJoin('m_user', 'm_user.id', '=', 't_submission.m_user_id')
      ->where('t_submission.m_form_id', $id)
      ->when($from, fn($qq) => $qq->where('t_submission.submitted_at', '>=', $from))
      ->when($to,   fn($qq) => $qq->where('t_submission.submitted_at', '<=', $to))
      ->when($qSearch !== '', function ($qq) use ($qSearch) {
        $qq->where(function ($w) use ($qSearch) {
          $w->where('m_user.name', 'like', "%{$qSearch}%")
            ->orWhere('m_user.email', 'like', "%{$qSearch}%");
        });
      })
      // ->where('t_submission.is_valid', true)
      ->orderByDesc('t_submission.submitted_at')
      ->get()
      ->map(function ($r) {
        return [
          'id'            => $r->id,
          'submitted_at'  => $r->submitted_at ? Carbon::parse($r->submitted_at)->format('Y-m-d H:i') : null,
          'name'          => $r->name ?? '—',
          // 'role'          => $r->role ?? '-',
          // 'study_program' => $r->study_program ?? '-',
          'status'        => $r->is_valid ? 'valid' : 'invalid',
        ];
      });

    return response()->json(['data' => $rows]);
  }

  public function respondentsExport(Request $req, string $formId)
  {
    [$from, $to] = FormHelper::dateRange($req);
    $qSearch = trim((string) $req->get('q', ''));

    // ambil semua pertanyaan (urut sesuai sequence)
    $questions = Question::where('m_form_id', $formId)
      ->orderBy('sequence')
      ->get(['id', 'question', 'type']);

    // ambil semua submission valid + user
    $submissions = Submission::query()
      ->select('t_submission.id', 't_submission.submitted_at', 't_submission.is_valid', 'm_user.name')
      ->leftJoin('m_user', 'm_user.id', '=', 't_submission.m_user_id')
      ->where('t_submission.m_form_id', $formId)
      ->when($from, fn($q) => $q->where('t_submission.submitted_at', '>=', $from))
      ->when($to,   fn($q) => $q->where('t_submission.submitted_at', '<=', $to))
      ->when($qSearch !== '', function ($q) use ($qSearch) {
        $q->where(function ($w) use ($qSearch) {
          $w->where('m_user.name', 'like', "%{$qSearch}%")
            ->orWhere('m_user.email', 'like', "%{$qSearch}%");
        });
      })
      ->orderByDesc('t_submission.submitted_at')
      ->get();

    // ambil semua jawaban sekali join
    $answers = Answer::query()
      ->select([
        't_answer.id',
        't_answer.text_value',
        't_answer.m_question_id',
        't_submission.id as submission_id',
        't_submission_target.id as target_id',
        'm_question.type as q_type',
        'm_question_option.answer as opt_label'
      ])
      ->join('t_submission_target', 't_submission_target.id', '=', 't_answer.t_submission_target_id')
      ->join('t_submission', 't_submission.id', '=', 't_submission_target.t_submission_id')
      ->join('m_question', 'm_question.id', '=', 't_answer.m_question_id')
      ->leftJoin('m_question_option', 'm_question_option.id', '=', 't_answer.m_question_option_id')
      ->where('t_submission.m_form_id', $formId)
      ->when($from, fn($q) => $q->where('t_submission.submitted_at', '>=', $from))
      ->when($to,   fn($q) => $q->where('t_submission.submitted_at', '<=', $to))
      ->get();

    // ambil multi (checkbox) dari t_answer_option
    $multiAnswers = DB::table('t_answer_option as ao')
      ->select([
        'a.m_question_id',
        'ao.t_answer_id',
        'qo.answer as opt_label'
      ])
      ->join('t_answer as a', 'a.id', '=', 'ao.t_answer_id')
      ->join('t_submission_target as st', 'st.id', '=', 'a.t_submission_target_id')
      ->join('t_submission as s', 's.id', '=', 'st.t_submission_id')
      ->join('m_question_option as qo', 'qo.id', '=', 'ao.m_question_option_id')
      ->where('s.m_form_id', $formId)
      ->get()
      ->groupBy('t_answer_id');

    // map jawaban per submission id
    $answerMap = [];
    foreach ($answers as $a) {
      $key = $a->submission_id;
      $qid = $a->m_question_id;

      // handle checkbox
      if ($a->q_type === 'checkbox') {
        $options = $multiAnswers[$a->id] ?? collect();
        $joined = $options->pluck('opt_label')->join(', ');
        $answerMap[$key][$qid] = $joined;
      }
      // handle option
      elseif ($a->q_type === 'option') {
        $answerMap[$key][$qid] = $a->opt_label;
      }
      // handle text
      else {
        $answerMap[$key][$qid] = $a->text_value;
      }
    }

    // siapkan data rows dengan dynamic kolom pertanyaan
    $headers = ['ID Responden', 'Waktu Submit', 'Nama Responden', 'Status Validasi'];
    foreach ($questions as $q) $headers[] = $q->question;

    $rows = [];
    foreach ($submissions as $s) {
      $row = [
        $s->id,
        Carbon::parse($s->submitted_at)->format('Y-m-d H:i'),
        $s->name ?? '—',
        $s->is_valid ? 'valid' : 'invalid'
      ];

      foreach ($questions as $q) {
        $val = $answerMap[$s->id][$q->id] ?? '';
        $row[] = $val;
      }

      $rows[] = $row;
    }

    $filename = 'form_' . $formId . '_responses_' . now()->format('Ymd_His') . '.xlsx';
    return Excel::download(new \App\Exports\ExportFormRespondents($rows, $headers), $filename);
  }


  public function respondentDetail(Request $req, string $id, string $submissionId)
  {
    $sub = Submission::query()
      ->with('user')
      ->where('id', $submissionId)
      ->where('m_form_id', $id)
      ->firstOrFail();

    $profile = [
      'name'         => optional($sub->user)->name ?? '—',
      'role'         => optional($sub->user)->role_label ?? null,
      'study_program' => optional($sub->user)->study_program_name ?? null,
      'submitted_at' => optional($sub->submitted_at)->format('Y-m-d H:i'),
    ];

    // Ambil target (kalau kamu pakai t_submission_target)
    $target = SubmissionTarget::where('t_submission_id', $sub->id)->first();

    // Ambil jawaban beserta pertanyaan
    // Relasi: Answer belongsTo Question (m_question_id)
    // penting: eager load both paths
    $answers = Answer::query()
      ->with([
        'question:id,question,type,sequence',
        // untuk checkbox (multi):
        'answerOptions.questionOption:id,answer,point',
        // untuk option (single):
        'questionOption:id,answer,point', // <-- bikin relasi belongsTo di model Answer
      ])
      ->where('t_submission_target_id', optional($target)->id)
      ->orderByRaw('COALESCE((SELECT sequence FROM m_question WHERE m_question.id = t_answer.m_question_id), 9999) asc')
      ->get()
      ->map(function ($a) {
        $q = $a->question;
        if (!$q) return null;

        if ($q->type === 'text') {
          return [
            'q'    => $q->question,
            'type' => 'text',
            'value' => $a->text_value ?? null,
          ];
        }

        if ($q->type === 'option') {
          // single: ambil dari Answer->questionOption
          $opt = $a->questionOption;
          $opts = $opt ? [['answer' => $opt->answer, 'point' => (int)($opt->point ?? 0)]] : [];
          return [
            'q'       => $q->question,
            'type'    => 'option',
            'options' => $opts,
          ];
        }

        // checkbox: ambil dari answerOptions
        $opts = [];
        foreach ($a->answerOptions as $ao) {
          if ($ao->questionOption) {
            $opts[] = [
              'answer' => $ao->questionOption->answer,
              'point'  => (int) ($ao->questionOption->point ?? 0),
            ];
          }
        }
        return [
          'q'       => $q->question,
          'type'    => 'checkbox',
          'options' => $opts,
        ];
      })
      ->filter()
      ->values();

    return response()->json([
      'profile' => $profile,
      'answers' => $answers,
    ]);
  }

  public function questionStats(Request $req, string $formId)
  {
    [$from, $to] = FormHelper::dateRange($req);
    $qSearch = trim((string) $req->get('q', ''));

    // Ambil pertanyaan tipe pilihan
    $questions = Question::query()
      ->where('m_form_id', $formId)
      ->whereIn('type', ['checkbox', 'option'])
      ->orderBy('sequence')
      ->get(['id', 'question', 'type']);

    if ($questions->isEmpty()) return response()->json([]);

    $single = DB::table('t_submission as s')
      ->join('t_submission_target as st', 'st.t_submission_id', '=', 's.id')
      ->join('t_answer as a', 'a.t_submission_target_id', '=', 'st.id')
      ->join('m_question as q', 'q.id', '=', 'a.m_question_id')
      ->join('m_question_option as qo', 'qo.id', '=', 'a.m_question_option_id')
      ->leftJoin('m_user as u', 'u.id', '=', 's.m_user_id')
      ->where('s.m_form_id', $formId)
      ->where('s.is_valid', true)
      ->when($from, fn($qq) => $qq->where('s.submitted_at', '>=', $from))
      ->when($to,   fn($qq) => $qq->where('s.submitted_at', '<=', $to))
      ->when($qSearch !== '', function ($qq) use ($qSearch) {
        $qq->where(function ($w) use ($qSearch) {
          $w->where('u.name', 'like', "%{$qSearch}%")
            ->orWhere('u.email', 'like', "%{$qSearch}%");
        });
      })
      ->whereNotNull('a.m_question_option_id')
      ->groupBy('q.id', 'qo.id', 'qo.answer', 'qo.point', 'qo.sequence')
      ->select([
        'q.id as question_id',
        'qo.id as option_id',
        'qo.answer as option_label',
        DB::raw('COALESCE(qo.point,0) as point'),
        DB::raw('COUNT(*) as cnt'),
        'qo.sequence',
      ]);

    // --- Query 2: MULTI (checkbox) lewat t_answer_option ---
    $multi = DB::table('t_submission as s')
      ->join('t_submission_target as st', 'st.t_submission_id', '=', 's.id')
      ->join('t_answer as a', 'a.t_submission_target_id', '=', 'st.id')
      ->join('t_answer_option as ao', 'ao.t_answer_id', '=', 'a.id')
      ->join('m_question as q', 'q.id', '=', 'a.m_question_id')
      ->join('m_question_option as qo', 'qo.id', '=', 'ao.m_question_option_id')
      ->leftJoin('m_user as u', 'u.id', '=', 's.m_user_id')
      ->where('s.m_form_id', $formId)
      ->where('s.is_valid', true)
      ->when($from, fn($qq) => $qq->where('s.submitted_at', '>=', $from))
      ->when($to,   fn($qq) => $qq->where('s.submitted_at', '<=', $to))
      ->when($qSearch !== '', function ($qq) use ($qSearch) {
        $qq->where(function ($w) use ($qSearch) {
          $w->where('u.name', 'like', "%{$qSearch}%")
            ->orWhere('u.email', 'like', "%{$qSearch}%");
        });
      })
      ->groupBy('q.id', 'qo.id', 'qo.answer', 'qo.point', 'qo.sequence')
      ->select([
        'q.id as question_id',
        'qo.id as option_id',
        'qo.answer as option_label',
        DB::raw('COALESCE(qo.point,0) as point'),
        DB::raw('COUNT(*) as cnt'),
        'qo.sequence',
      ]);

    // Union keduanya
    $union = $single->unionAll($multi);

    // Bungkus sebagai subquery lalu agregasi ulang (kalau-kalau ada overlap)
    $rows = DB::query()->fromSub($union, 'x')
      ->groupBy('x.question_id', 'x.option_id', 'x.option_label', 'x.point', 'x.sequence')
      ->orderBy('x.question_id')
      ->orderBy('x.sequence')
      ->get([
        'x.question_id',
        'x.option_id',
        'x.option_label',
        'x.point',
        DB::raw('SUM(x.cnt) as cnt'),
        'x.sequence',
      ]);

    // Hitung total per question
    $totals = $rows->groupBy('question_id')->map(fn($g) => $g->sum('cnt'));

    // Bentuk output per question
    $out = [];
    foreach ($questions as $q) {
      $group = $rows->where('question_id', $q->id)->sortBy('sequence')->values();

      // IMPORTANT: sertakan opsi yang "tidak pernah dipilih" (count 0), supaya chart tetap tampil lengkap
      $allOptions = QuestionOption::where('m_question_id', $q->id)
        ->orderBy('sequence')
        ->get(['id', 'answer', 'point', 'sequence']);

      $labels = [];
      $counts = [];
      $points = [];

      foreach ($allOptions as $opt) {
        $found = $group->firstWhere('option_id', $opt->id);
        $labels[] = $opt->answer;
        $counts[] = (int) ($found->cnt ?? 0);
        $points[] = (int) ($opt->point ?? 0);
      }

      $total = max(1, (int) ($totals[$q->id] ?? array_sum($counts)));

      $out[$q->id] = [
        'labels'   => $labels,
        'counts'   => $counts,
        'percents' => array_map(fn($c) => $total ? round($c * 100 / $total, 1) : 0, $counts),
        'points'   => $points,
      ];
    }

    return response()->json($out);
  }

  public function questionTexts(Request $req, string $formId, string $questionId)
  {
    [$from, $to] = FormHelper::dateRange($req);
    $qSearch = trim((string) $req->get('q', ''));
    $all = (int) $req->get('all', 0) === 1;

    // Validasi pertanyaannya text
    $q = Question::where('id', $questionId)->where('m_form_id', $formId)->firstOrFail();
    if ($q->type !== 'text') {
      return response()->json(['examples' => [], 'total' => 0]);
    }

    $base = DB::table('t_submission as s')
      ->join('t_submission_target as st', 'st.t_submission_id', '=', 's.id')
      ->join('t_answer as a', 'a.t_submission_target_id', '=', 'st.id')
      ->leftJoin('m_user as u', 'u.id', '=', 's.m_user_id')
      ->where('s.m_form_id', $formId)
      ->where('s.is_valid', true)
      ->where('a.m_question_id', $questionId)
      ->when($from, fn($qq) => $qq->where('s.submitted_at', '>=', $from))
      ->when($to,   fn($qq) => $qq->where('s.submitted_at', '<=', $to))
      ->when($qSearch !== '', function ($qq) use ($qSearch) {
        $qq->where(function ($w) use ($qSearch) {
          $w->where('u.name', 'like', "%{$qSearch}%")
            ->orWhere('u.email', 'like', "%{$qSearch}%");
        });
      });

    $total = (clone $base)->whereNotNull('a.text_value')->count();

    if ($all) {
      $allRows = (clone $base)
        ->whereNotNull('a.text_value')
        ->orderByDesc('s.submitted_at')
        ->limit(2000) // batas aman; bisa diubah
        ->get(['a.text_value']);
      return response()->json([
        'all'   => $allRows->map(fn($r) => ['text' => $r->text_value])->all(),
        'total' => $total,
      ]);
    } else {
      $examples = (clone $base)
        ->whereNotNull('a.text_value')
        ->orderByDesc('s.submitted_at')
        ->limit(5)
        ->get(['a.text_value']);
      return response()->json([
        'examples' => $examples->map(fn($r) => ['text' => $r->text_value])->all(),
        'total'    => $total,
      ]);
    }
  }
}
