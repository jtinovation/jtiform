<?php

namespace App\Helpers;

use App\Enums\FormTypeEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Helpers\StudyProgramHelper;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;

class HomeHelper
{
  public static function getKpis(array $filter = [])
  {
    // === 2) Daftar periode (ordered terbaru -> lama) ===
    $periods = DB::select("
          SELECT session_id, is_even, MAX(end_at) AS max_end
          FROM m_form
          WHERE type = 'lecture_evaluation'
          GROUP BY session_id, is_even
          ORDER BY max_end DESC
        ");

    // === 3) Tentukan current & previous period ===
    $current = null;
    $prev = null;

    if ($filter['session_id'] !== null && $filter['is_even'] !== null) {
      // pakai pilihan user sebagai current
      foreach ($periods as $idx => $p) {
        if ($p->session_id === $filter['session_id'] && (int)$p->is_even === (int)$filter['is_even']) {
          $current = $p;
          $prev = $periods[$idx + 1] ?? null;
          break;
        }
      }
      // kalau tidak ketemu di daftar, fallback ke paling baru
      if (!$current && !empty($periods)) {
        $current = $periods[0];
        $prev = $periods[1] ?? null;
      }
    } else {
      // tidak dipilih: pakai periode terbaru
      if (!empty($periods)) {
        $current = $periods[0];
        $prev = $periods[1] ?? null;
      }
    }

    $currentSessionId = $filter['session_id'] ?? ($current->session_id ?? null);
    $currentIsEven    = $filter['is_even']    ?? (isset($current->is_even) ? (int)$current->is_even : null);
    $prevSessionId    = $prev->session_id ?? null;
    $prevIsEven       = isset($prev->is_even) ? (int)$prev->is_even : null;

    // === 4) Helper untuk apply scope form ===
    $applyFormScope = function ($q, $sessId, $isEven, $filter) {
      // Session/Semester
      if ($sessId) $q->where('f.session_id', $sessId);
      if (!is_null($isEven)) $q->where('f.is_even', $isEven);

      // Type
      if (!empty($filter['type'])) {
        $q->where('f.type', $filter['type']);
      }

      // Status
      if ($filter['status'] === 'active') {
        $q->where('f.is_active', 1)
          ->whereRaw('NOW() BETWEEN f.start_at AND f.end_at');
      } elseif ($filter['status'] === 'finished') {
        $q->whereRaw('f.end_at < NOW()');
      }

      // Jurusan/Prodi scope -> via EXISTS ke t_submission_target
      if (!empty($filter['major_id']) || !empty($filter['study_program_id'])) {
        $q->whereExists(function ($ex) use ($filter) {
          $ex->select(DB::raw(1))
            ->from('t_submission as xs')
            ->join('t_submission_target as xt', 'xt.t_submission_id', '=', 'xs.id')
            ->whereColumn('xs.m_form_id', 'f.id');
          if (!empty($filter['major_id'])) {
            // match salah satu: major dosen atau major mata kuliah
            $ex->where(function ($w) use ($filter) {
              $w->where('xt.target_employee_major_id', $filter['major_id'])
                ->orWhere('xt.target_employee_major_id', $filter['major_id']); // bisa disesuaikan jika ada kolom major lain
            });
          }
          if (!empty($filter['study_program_id'])) {
            $ex->where('xt.target_subject_study_program_id', $filter['study_program_id']);
          }
        });
      }

      return $q;
    };

    // === 5) KPI: Form Aktif (count DISTINCT form) ===
    $activeNow = DB::table('m_form as f')
      ->selectRaw('COUNT(DISTINCT f.id) as c')
      ->when(true, fn($q) => $applyFormScope($q, $currentSessionId, $currentIsEven, $filter))
      // Jika status tidak dipilih dan type tidak dipilih, agar sejalan dengan “form aktif” default:
      ->when(empty($filter['status']) && empty($filter['type']), function ($q) {
        $q->where('f.type', 'lecture_evaluation')
          ->where('f.is_active', 1)
          ->whereRaw('NOW() BETWEEN f.start_at AND f.end_at');
      })
      ->value('c');

    $activePrev = 0;
    if ($prevSessionId !== null && $prevIsEven !== null) {
      $activePrev = DB::table('m_form as f')
        ->selectRaw('COUNT(DISTINCT f.id) as c')
        ->when(true, fn($q) => $applyFormScope($q, $prevSessionId, $prevIsEven, $filter))
        // Untuk prev, jika status tak dipilih, pakai definisi "aktif" saat periode tsb (opsional):
        ->when(empty($filter['status']) && empty($filter['type']), function ($q) {
          $q->where('f.type', 'lecture_evaluation');
        })
        ->value('c');
    }

    // === 6) KPI: Total Responden Valid (join form + scope target) ===
    $validNowQ = DB::table('t_submission as s')
      ->join('m_form as f', 'f.id', '=', 's.m_form_id')
      ->where('s.is_valid', 1);
    $applyFormScope($validNowQ, $currentSessionId, $currentIsEven, $filter);
    // Scope major/prodi via EXISTS pada target agar tidak menggandakan count
    if (!empty($filter['major_id']) || !empty($filter['study_program_id'])) {
      $validNowQ->whereExists(function ($ex) use ($filter) {
        $ex->select(DB::raw(1))
          ->from('t_submission_target as xt')
          ->whereColumn('xt.t_submission_id', 's.id');
        if (!empty($filter['major_id'])) {
          $ex->where(function ($w) use ($filter) {
            $w->where('xt.target_employee_major_id', $filter['major_id']);
          });
        }
        if (!empty($filter['study_program_id'])) {
          $ex->where('xt.target_subject_study_program_id', $filter['study_program_id']);
        }
      });
    }
    $validNow = (int) $validNowQ->count();

    $validPrev = 0;
    if ($prevSessionId !== null && $prevIsEven !== null) {
      $validPrevQ = DB::table('t_submission as s')
        ->join('m_form as f', 'f.id', '=', 's.m_form_id')
        ->where('s.is_valid', 1);
      $applyFormScope($validPrevQ, $prevSessionId, $prevIsEven, $filter);
      if (!empty($filter['major_id']) || !empty($filter['study_program_id'])) {
        $validPrevQ->whereExists(function ($ex) use ($filter) {
          $ex->select(DB::raw(1))
            ->from('t_submission_target as xt')
            ->whereColumn('xt.t_submission_id', 's.id');
          if (!empty($filter['major_id'])) {
            $ex->where(function ($w) use ($filter) {
              $w->where('xt.target_employee_major_id', $filter['major_id']);
            });
          }
          if (!empty($filter['study_program_id'])) {
            $ex->where('xt.target_subject_study_program_id', $filter['study_program_id']);
          }
        });
      }
      $validPrev = (int) $validPrevQ->count();
    }

    // === 7) KPI: Rata-rata Skor Dosen (m_report) + scope jurusan/prodi di kolom *_employee
    $avgNowQ = DB::table('m_report as r')
      ->join('m_form as f', 'f.id', '=', 'r.m_form_id')
      ->when(true, fn($q) => $applyFormScope($q, $currentSessionId, $currentIsEven, array_merge($filter, ['type' => $filter['type'] ?? 'lecture_evaluation'])));
    // scope major/prodi
    if (!empty($filter['major_id'])) {
      $avgNowQ->where('r.m_major_id_employee', $filter['major_id']);
    }
    if (!empty($filter['study_program_id'])) {
      $avgNowQ->where('r.m_study_program_id_employee', $filter['study_program_id']);
    }
    $avgNow = $avgNowQ->avg('r.overall_average_score') ?? 0;

    $avgPrev = null;
    if ($prevSessionId !== null && $prevIsEven !== null) {
      $avgPrevQ = DB::table('m_report as r')
        ->join('m_form as f', 'f.id', '=', 'r.m_form_id');
      $applyFormScope($avgPrevQ, $prevSessionId, $prevIsEven, array_merge($filter, ['type' => $filter['type'] ?? 'lecture_evaluation']));
      if (!empty($filter['major_id'])) {
        $avgPrevQ->where('r.m_major_id_employee', $filter['major_id']);
      }
      if (!empty($filter['study_program_id'])) {
        $avgPrevQ->where('r.m_study_program_id_employee', $filter['study_program_id']);
      }
      $avgPrev = $avgPrevQ->avg('r.overall_average_score');
    }

    // === 8) KPI: Partisipasi (proxy) ===
    $participationNow  = $activeNow  > 0 ? round(($validNow  / $activeNow)  * 100, 2) : 0.0;
    $participationPrev = $activePrev > 0 ? round(($validPrev / $activePrev) * 100, 2) : 0.0;

    // === 9) Trend helper ===
    $trend = fn($now, $prev) => ($prev && abs($prev) > 0)
      ? round((($now - $prev) / abs($prev)) * 100, 2)
      : null;

    $kpis = [
      'active_forms' => [
        'now' => (int) $activeNow,
        'prev' => (int) $activePrev,
        'trend' => $trend($activeNow, $activePrev),
      ],
      'valid_submissions' => [
        'now' => (int) $validNow,
        'prev' => (int) $validPrev,
        'trend' => $trend($validNow, $validPrev),
      ],
      'avg_score' => [
        'now' => $avgNow ? round($avgNow, 2) : null,
        'prev' => $avgPrev ? round($avgPrev, 2) : null,
        'trend' => ($avgPrev && abs($avgPrev) > 0) ? round((($avgNow - $avgPrev) / abs($avgPrev)) * 100, 2) : null,
      ],
      'participation_pct' => [
        'now' => $participationNow,
        'prev' => $participationPrev,
        'trend' => $trend($participationNow, $participationPrev),
        'note' => 'Proxy sementara: valid_submissions / active_forms',
      ],
    ];

    return $kpis;
  }

  public static function buildProdiChart(array $filter): array
  {
    $currentSessionId = $filter['session_id'] ?? null;
    $currentIsEven    = $filter['is_even']    ?? null;
    // Base query: hitung rata-rata skor 1..5 per prodi (weighted by respondents)
    $q = DB::table('prodi_report as pr')
      ->select([
        'pr.m_study_program_id',
        DB::raw("
                SUM(
                  ((5*pr.percentage_score_5
                  + 4*pr.percentage_score_4
                  + 3*pr.percentage_score_3
                  + 2*pr.percentage_score_2
                  + 1*pr.percentage_score_1) / 100.0)
                  * pr.total_respondents
                ) / NULLIF(SUM(pr.total_respondents),0) AS mean_score
            ")
      ])
      ->when($currentSessionId, fn($qq) => $qq->where('pr.m_session_id', $currentSessionId))
      ->when(!is_null($currentIsEven), fn($qq) => $qq->where('pr.is_even', $currentIsEven))
      ->when(!empty($filter['major_id']), fn($qq) => $qq->where('pr.m_major_id', $filter['major_id']))
      ->groupBy('pr.m_study_program_id');

    // Urutan: highest first
    $q->orderByDesc('mean_score');

    // Jika TIDAK pilih jurusan => top 7 saja
    if (empty($filter['major_id'])) {
      $q->limit(7);
    }

    $rows = $q->get();

    // Ambil nama prodi (opsional): jika ada tabel m_study_program
    // Fallback: pakai id jika nama tidak ada
    $studyProgramIds = $rows->pluck('m_study_program_id')->filter()->all();
    $nameMap = [];

    if (!empty($studyProgramIds) && Schema::hasTable('m_study_program')) {
      $nameMap = DB::table('m_study_program')
        ->whereIn('id', $studyProgramIds)
        ->pluck('name', 'id')
        ->toArray();
    } else {
      $nameMap = StudyProgramHelper::getAsOptions(Auth::user()->token, $filter['major_id'] ?? null)['data'] ?? [];
    }

    $labels = [];
    $data   = [];

    foreach ($rows as $r) {
      $id    = $r->m_study_program_id ?? '-';
      $name = $id;
      if (is_array($nameMap)) {
        if (isset($nameMap[$id]) && is_string($nameMap[$id])) {
          $name = $nameMap[$id];
        } else {
          foreach ($nameMap as $opt) {
            if (!is_array($opt)) continue;
            $val = $opt['value'] ?? $opt['id'] ?? null;
            if ($val !== null && (string)$val === (string)$id) {
              $name = $opt['label'] ?? ($opt['name'] ?? $id);
              break;
            }
          }
        }
      }
      $score = $r->mean_score !== null ? round((float)$r->mean_score, 2) : 0.0;

      $labels[] = $name;
      $data[]   = $score; // skala 1–5
    }

    return [
      'labels' => $labels,
      'data'   => $data,
      'unit'   => '1-5', // info untuk axis label
    ];
  }

  public static function buildQuestionScoreStack(array $filter): array
  {
    $formMeta = DB::table('m_form')
      ->where('type', FormTypeEnum::LECTURE_EVALUATION->value)
      ->when($filter['session_id'], fn($q) => $q->where('session_id', $filter['session_id']))
      ->when($filter['is_even'], fn($q) => $q->where('is_even', $filter['is_even']))
      ->latest()
      ->first(['session_id', 'is_even', 'id']);
    // Ambil pertanyaan yang mau ditampilkan (urut sequence)
    $questions = DB::table('m_question')
      ->where('m_form_id', $formMeta->id)
      // jika hanya mau yg bertipe memilih skor:
      ->whereIn('type', ['option', 'checkbox'])
      ->orderBy('sequence', 'asc')
      ->get(['id', 'question', 'sequence']);

    if ($questions->isEmpty()) {
      return ['categories' => [], 'series' => []];
    }

    $questionIds = $questions->pluck('id')->all();

    // Jika user tidak memilih session/semester, tapi milih form,
    // kita bisa turunkan session/is_even dari m_form agar "apple to apple"
    $sessionId = $filter['session_id'] ?? ($formMeta->session_id ?? null);
    $isEven    = array_key_exists('is_even', $filter) ? $filter['is_even'] : ($formMeta->is_even ?? null);

    // Agregasi cepat dari prodi_report:
    $rows = DB::table('prodi_report as pr')
      ->select([
        'pr.m_question_id',
        DB::raw('SUM(pr.total_respondents * pr.percentage_score_5 / 100.0) AS c5'),
        DB::raw('SUM(pr.total_respondents * pr.percentage_score_4 / 100.0) AS c4'),
        DB::raw('SUM(pr.total_respondents * pr.percentage_score_3 / 100.0) AS c3'),
        DB::raw('SUM(pr.total_respondents * pr.percentage_score_2 / 100.0) AS c2'),
        DB::raw('SUM(pr.total_respondents * pr.percentage_score_1 / 100.0) AS c1'),
      ])
      ->whereIn('pr.m_question_id', $questionIds)
      ->when($sessionId, fn($q) => $q->where('pr.m_session_id', $sessionId))
      ->when(!is_null($isEven), fn($q) => $q->where('pr.is_even', (int)$isEven))
      ->when(!empty($filter['major_id']), fn($q) => $q->where('pr.m_major_id', $filter['major_id']))
      ->when(!empty($filter['study_program_id']), fn($q) => $q->where('pr.m_study_program_id', $filter['study_program_id']))
      ->groupBy('pr.m_question_id')
      ->get()
      ->keyBy('m_question_id');

    // Bentuk data untuk ApexCharts (stacked bar)
    $categories = [];
    $seriesMap = [5 => [], 4 => [], 3 => [], 2 => [], 1 => []];

    foreach ($questions as $q) {
      $label = $q->sequence . '. ' . $q->question;
      $categories[] = $label;

      $agg = $rows->get($q->id);
      $c5 = $agg?->c5 ?? 0;
      $c4 = $agg?->c4 ?? 0;
      $c3 = $agg?->c3 ?? 0;
      $c2 = $agg?->c2 ?? 0;
      $c1 = $agg?->c1 ?? 0;

      // bulatkan ke integer count (kalau perlu)
      $seriesMap[5][] = (int) round($c5);
      $seriesMap[4][] = (int) round($c4);
      $seriesMap[3][] = (int) round($c3);
      $seriesMap[2][] = (int) round($c2);
      $seriesMap[1][] = (int) round($c1);
    }

    return [
      'categories' => $categories,
      'series' => [
        ['name' => 'Skor 5', 'data' => $seriesMap[5]],
        ['name' => 'Skor 4', 'data' => $seriesMap[4]],
        ['name' => 'Skor 3', 'data' => $seriesMap[3]],
        ['name' => 'Skor 2', 'data' => $seriesMap[2]],
        ['name' => 'Skor 1', 'data' => $seriesMap[1]],
      ],
    ];
  }

  public static function lectureReportData(?string $reportId = null, ?string $userId = null): array
  {
    $reports = Report::where('m_user_id', $userId ?? Auth::id())
      ->with(['form.questions' => fn($q) => $q->orderBy('sequence', 'asc')])
      ->where('overall_average_score', '>', 0)
      ->orderBy('created_at', 'desc')
      ->get();

    if ($reports->isEmpty()) {
      return [
        'reports' => [],
        'selectedReport' => null,
        'kpi' => [
          'overall'   => null,
          'predicate' => null,
          'trendPct'  => null,
          'courses'   => 0,
          'respondents' => 0,
        ],
        'reportChartData' => [
          'chartLabels' => [],
          'chartData' => [],
          'chartPredicates' => [],
        ],
        'courseBar' => [
          'labels' => [],
          'data'   => [],
        ],
        'questionBar' => [
          'labels'    => [],
          'fullLabels' => [],
          'data'      => [],
        ],
        'coursesTable' => [],
      ];
    }

    // Pilihan report via dropdown (report_id) atau default = terbaru
    $selectedReport = $reports->firstWhere('id', $reportId) ?? $reports->first();

    // ===== KPI (dari selected report) =====
    $latestOverall = round($selectedReport->overall_average_score, 2); // 0-100
    $latestPredicate = $selectedReport->predicate;

    $prev = $reports->skip(1)->first(); // report sebelumnya (kalau ada)
    $trendPct = null;
    if ($prev && $prev->overall_average_score > 0) {
      $trendPct = round((($latestOverall - $prev->overall_average_score) / $prev->overall_average_score) * 100, 2);
    }

    $courses = collect($selectedReport->report_details ?? []);
    $totalRespondents = (int) $courses->sum('respondents');
    $totalCourses = (int) $courses->count();

    // Trend jumlah matakuliah dibanding report sebelumnya
    $courseTrendPct = null;
    if ($prev) {
      $prevCoursesCount = count($prev->report_details ?? []);
      if ($prevCoursesCount > 0) {
        $courseTrendPct = round((($totalCourses - $prevCoursesCount) / $prevCoursesCount) * 100, 2);
      }
    }

    // Trend jumlah responden dibanding report sebelumnya
    $respondentsTrendPct = null;
    if ($prev) {
      $prevRespondents = (int) collect($prev->report_details ?? [])->sum('respondents');
      if ($prevRespondents > 0) {
        $respondentsTrendPct = round((($totalRespondents - $prevRespondents) / $prevRespondents) * 100, 2);
      }
    }

    $kpi = [
      'overall'   => $latestOverall,
      'predicate' => $latestPredicate,
      'trendPct'  => $trendPct, // naik turun %
      'courses'   => $totalCourses,
      'coursesTrendPct' => $courseTrendPct,
      'respondents' => $totalRespondents,
      'respondentsTrendPct' => $respondentsTrendPct
    ];

    // ===== Area Chart "Rekapitulasi Rapor Evaluasi Saya" (punyamu) =====
    $reportChartData = [
      'chartLabels'    => $reports->pluck('form.code')->reverse()->values(),
      'chartData'      => $reports->pluck('overall_average_score')->reverse()->values(),
      'chartPredicates' => $reports->pluck('predicate')->reverse()->values(),
    ];

    // ===== Bar Chart: Rata-rata per Matakuliah (dari selected report_details) =====
    // Gunakan kode matkul + kelas sebagai label
    $courseBar = [
      'labels' => $courses->map(fn($c) => ($c['class'] ?? '') . ' ' . ($c['course_name'] ?? ''))->values(),
      'data'   => $courses->map(fn($c) => round((float)($c['average_score'] ?? 0), 2))->values(), // 0-100
    ];

    // ===== Radar Chart: Rata-rata per Pertanyaan (agregat weighted by respondents) =====
    // Ambil daftar pertanyaan dari form agar urutan konsisten
    $questions = $selectedReport->form?->questions ?? collect();
    // key = question_id, value = ['sum' => total skor*respondents, 'w' => total respondents]
    $agg = [];
    foreach ($courses as $c) {
      $w = (int) ($c['respondents'] ?? 0);
      foreach ($c['scores'] ?? [] as $s) {
        $qid = $s['question_id'] ?? null;
        $score = (float) ($s['score'] ?? 0); // 0-100
        if ($qid === null || $w <= 0) continue;
        if (!isset($agg[$qid])) $agg[$qid] = ['sum' => 0.0, 'w' => 0];
        $agg[$qid]['sum'] += $score * $w;
        $agg[$qid]['w']   += $w;
      }
    }
    // label singkat P1, P2, ...
    $questionShortLabels = [];
    $questionFullLabels  = [];
    $questionData        = [];

    foreach ($questions as $q) {
      $short = 'P' . $q->sequence;
      $full  = $q->sequence . '. ' . $q->question;

      $questionShortLabels[] = $short;
      $questionFullLabels[]  = $full;

      if (isset($agg[$q->id]) && $agg[$q->id]['w'] > 0) {
        $questionData[] = round($agg[$q->id]['sum'] / $agg[$q->id]['w'], 2);
      } else {
        $questionData[] = 0;
      }
    }

    $questionBar = [
      'labels'    => $questionShortLabels,  // P1, P2, ...
      'fullLabels' => $questionFullLabels,   // pertanyaan lengkap untuk tooltip
      'data'      => $questionData,         // 0–100
    ];

    // ===== Tabel ringkas matakuliah =====
    $coursesTable = $courses->map(function ($c, $i) {
      return [
        'no' => $i + 1,
        'class' => $c['class'] ?? '',
        'code'  => $c['course_code'] ?? '',
        'name'  => $c['course_name'] ?? '',
        'respondents' => (int) ($c['respondents'] ?? 0),
        'avg' => number_format((float) ($c['average_score'] ?? 0), 2),
      ];
    })->values()->all();

    return [
      'reports' => $reports,
      'selectedReport' => $selectedReport,
      'kpi' => $kpi,
      'reportChartData' => $reportChartData,
      'courseBar' => $courseBar,
      'questionBar' => $questionBar,
      'coursesTable' => $coursesTable,
    ];
  }
}
