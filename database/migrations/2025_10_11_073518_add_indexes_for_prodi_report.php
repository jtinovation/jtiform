<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::table('t_submission_target', function (Blueprint $table) {
      $table->index(['target_subject_study_program_id', 'target_employee_major_id', 'target_subject_semester_id'], 'idx_tst_filters');
    });

    Schema::table('t_answer', function (Blueprint $table) {
      $table->index(['t_submission_target_id', 'm_question_id', 'score'], 'idx_answer_target_question_score');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('t_submission_target', function (Blueprint $table) {
      $table->dropIndex('idx_tst_filters');
    });

    Schema::table('t_answer', function (Blueprint $table) {
      $table->dropIndex('idx_answer_target_question_score');
    });
  }
};
