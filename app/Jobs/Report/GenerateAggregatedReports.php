<?php

namespace App\Jobs\Report;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class GenerateAggregatedReports implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected string $studyProgramId;
  protected string $majorId;
  protected array $semesterIds;
  protected string $sessionId;
  protected bool $isEven;

  public function __construct(
    string $studyProgramId,
    string $majorId,
    string $sessionId,
    array $semesterIds,
    bool $isEven
  ) {
    $this->studyProgramId = $studyProgramId;
    $this->majorId = $majorId;
    $this->sessionId = $sessionId;
    $this->semesterIds = $semesterIds;
    $this->isEven = $isEven;
  }

  public function handle(): void
  {
    // Safety jika semesterIds kosong
    if (empty($this->semesterIds)) return;

    // Waktu sekarang untuk created_at/updated_at
    $now = now()->toDateTimeString();

    // Build placeholder semester IN (...)
    $inPlaceholders = implode(',', array_fill(0, count($this->semesterIds), '?'));

    // Catatan:
    // SUM(a.score=5) adalah MySQL idiom -> menghitung banyaknya nilai 5
    // ROUND(... * 100 / COUNT(*), 2) untuk persen
    $sql = "
            INSERT INTO prodi_report (
                id, m_study_program_id, m_major_id, m_session_id, is_even, m_question_id,
                total_respondents,
                percentage_score_5, percentage_score_4, percentage_score_3, percentage_score_2, percentage_score_1,
                created_at, updated_at
            )
            SELECT
                UUID() AS id,
                ? AS m_study_program_id,
                ? AS m_major_id,
                ? AS m_session_id,
                ? AS is_even,
                a.m_question_id,
                COUNT(*) AS total_respondents,
                ROUND(SUM(a.score = 5) * 100.0 / COUNT(*), 2) AS percentage_score_5,
                ROUND(SUM(a.score = 4) * 100.0 / COUNT(*), 2) AS percentage_score_4,
                ROUND(SUM(a.score = 3) * 100.0 / COUNT(*), 2) AS percentage_score_3,
                ROUND(SUM(a.score = 2) * 100.0 / COUNT(*), 2) AS percentage_score_2,
                ROUND(SUM(a.score = 1) * 100.0 / COUNT(*), 2) AS percentage_score_1,
                ? AS created_at,
                ? AS updated_at
            FROM t_answer a
            INNER JOIN t_submission_target t
                ON t.id = a.t_submission_target_id
            WHERE
                (? IS NULL OR t.target_subject_study_program_id = ?)
                AND (? IS NULL OR t.target_employee_major_id = ?)
                AND t.target_subject_semester_id IN ($inPlaceholders)
            GROUP BY a.m_question_id
            ON DUPLICATE KEY UPDATE
                total_respondents = VALUES(total_respondents),
                percentage_score_5 = VALUES(percentage_score_5),
                percentage_score_4 = VALUES(percentage_score_4),
                percentage_score_3 = VALUES(percentage_score_3),
                percentage_score_2 = VALUES(percentage_score_2),
                percentage_score_1 = VALUES(percentage_score_1),
                updated_at = VALUES(updated_at)
        ";

    // Bindings urut sesuai tanda tanya di atas
    $bindings = [
      $this->studyProgramId,
      $this->majorId,
      $this->sessionId,
      $this->isEven, // bisa null
      $now,
      $now,

      // WHERE nullable guards
      $this->studyProgramId,
      $this->studyProgramId,
      $this->majorId,
      $this->majorId,
      // semester ids
      ...$this->semesterIds,
    ];

    DB::statement($sql, $bindings);
  }
}
