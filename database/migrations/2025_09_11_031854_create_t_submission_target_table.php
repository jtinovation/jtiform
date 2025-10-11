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
    Schema::create('t_submission_target', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->foreignUuid('t_submission_id')
        ->constrained('t_submission')
        ->cascadeOnUpdate()
        ->cascadeOnDelete();
      $table->string('target_type');
      $table->uuid('target_id')->index()->nullable();
      $table->uuid('target_m_subject_semester_id')->index()->nullable();
      $table->uuid('target_subject_semester_id')->index()->nullable();
      $table->uuid('target_subject_id')->index()->nullable();
      $table->string('target_subject_name')->nullable();
      $table->string('target_subject_code')->nullable();
      $table->uuid('target_subject_study_program_id')->index()->nullable();
      $table->string('target_subject_study_program_name')->nullable();

      $table->uuid('target_employee_id')->index()->nullable();
      $table->uuid('target_employee_user_id')->index()->nullable();
      $table->string('target_employee_user_name')->index()->nullable();
      $table->uuid('target_employee_major_id')->index()->nullable();
      $table->string('target_employee_major_name')->nullable();
      $table->uuid('target_employee_study_program_id')->index()->nullable();
      $table->string('target_employee_study_program_name')->nullable();
      $table->integer('old_db_id')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('t_submission_target');
  }
};
