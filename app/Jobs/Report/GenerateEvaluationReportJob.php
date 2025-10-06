<?php

namespace App\Jobs\Report;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GenerateEvaluationReportJob implements ShouldQueue
{
  use Queueable;

  protected $subjectLectures;
  protected $formId;

  /**
   * Create a new job instance.
   */
  public function __construct(array $subjectLectures, string $formId)
  {
    $this->subjectLectures = $subjectLectures;
    $this->formId = $formId;
  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    $subjectLectureIds = Arr::pluck($this->subjectLectures, 'id');
    $maxPointsPerQuestion = 5;

    $stats = DB::table('t_answer as a')
      ->join('t_submission_target as st', 'a.t_submission_target_id', '=', 'st.id')
      ->whereIn('st.target_id', $subjectLectureIds)
      ->select(
        'st.target_id',
        'a.m_question_id',
        DB::raw("ROUND((SUM(a.score) * 100.0) / (COUNT(a.id) * ?), 2) as percentage_score")
      )
      ->groupBy('st.target_id', 'a.m_question_id')
      ->addBinding($maxPointsPerQuestion, 'select')
      ->get();

    $statsBySubjectLecture = $stats->groupBy('target_id');

    $respondents = DB::table('t_submission_target')
      ->whereIn('target_id', $subjectLectureIds)
      ->select('target_id', DB::raw('COUNT(id) as total'))
      ->groupBy('target_id')
      ->get()
      ->keyBy('target_id');

    $questions = DB::table('m_question')
      ->where('m_form_id', $this->formId)
      ->orderBy('sequence', 'asc')
      ->get()
      ->keyBy('id');

    $lecturesByEmployee = collect($this->subjectLectures)->groupBy('employee_id');
    $allFinalReports = [];
    $now = now();

    foreach ($lecturesByEmployee as $employeeId => $subjectLecturesForEmployee) {
      $reportDetails = [];
      $totalScoreOverall = 0;
      $courseCountWithRespondents = 0;
      $totalRespondentsOverall = 0;

      foreach ($subjectLecturesForEmployee as $subjectLecture) {
        $subjectLectureId = Arr::get($subjectLecture, 'id');
        $respondentsCount = $respondents->get($subjectLectureId)?->total ?? 0;

        $courseScores = [];
        $totalCourseScore = 0;

        $courseStats = $statsBySubjectLecture->get($subjectLectureId);

        foreach ($questions as $questionId => $question) {
          $score = 0.00;
          if ($courseStats) {
            $questionStat = $courseStats->firstWhere('m_question_id', $questionId);
            $score = $questionStat?->percentage_score ?? 0.00;
          }

          $courseScores[] = [
            "question_id" => $questionId,
            "score" => (float) $score
          ];
          $totalCourseScore += $score;
        }

        $courseAverage = 0.00;
        if ($questions->count() > 0 && $respondentsCount > 0) {
          $courseAverage = round($totalCourseScore / $questions->count(), 2);
          $totalScoreOverall += $courseAverage;
          $courseCountWithRespondents++;
        }

        $reportDetails[] = [
          "course_code" => Arr::get($subjectLecture, 'subject_semester.subject.code'),
          "course_name" => Arr::get($subjectLecture, 'subject_semester.subject.name'),
          "class" => Arr::get($subjectLecture, 'subject_semester.subject.study_program.code'),
          "respondents" => $respondentsCount,
          "average_score" => $courseAverage,
          "scores" => $courseScores
        ];

        $totalRespondentsOverall += $respondentsCount;
      }

      $overallAverageScore = ($courseCountWithRespondents > 0) ? round($totalScoreOverall / $courseCountWithRespondents, 2) : 0.00;

      $predicate = 'Kurang';
      if ($overallAverageScore <= 20) {
        $predicate = 'Sangat Kurang';
      } elseif ($overallAverageScore > 20 && $overallAverageScore <= 40) {
        $predicate = 'Kurang';
      } elseif ($overallAverageScore > 40 && $overallAverageScore <= 60) {
        $predicate = 'Cukup';
      } elseif ($overallAverageScore > 60 && $overallAverageScore <= 80) {
        $predicate = 'Baik';
      } else {
        $predicate = 'Sangat Baik';
      }

      $allFinalReports[] = [
        'id' => Str::uuid()->toString(),
        'm_user_id' => $subjectLecturesForEmployee->where('employee_id', $employeeId)->first()['user_id'],
        'm_employee_id' => $employeeId, // ID dosen yang sedang diproses
        'm_form_id' => $this->formId,
        'report_details' => json_encode($reportDetails),
        'overall_average_score' => $overallAverageScore,
        'predicate' => $predicate,
        'total_respondents' => $totalRespondentsOverall,
        'created_at' => $now,
        'updated_at' => $now,
      ];
    }

    if (!empty($allFinalReports)) {
      DB::table('m_report')->insert($allFinalReports);
    }
  }
}
