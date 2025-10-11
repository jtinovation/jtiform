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
    Schema::create('prodi_report', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->uuid('m_study_program_id')->index()->nullable();
      $table->uuid('m_major_id')->index()->nullable();
      $table->uuid('m_session_id')->index()->nullable();
      $table->boolean('is_even')->nullable();
      $table->foreignUuid('m_question_id')->constrained('m_question')->onDelete('cascade');
      $table->unsignedInteger('total_respondents');
      $table->decimal('percentage_score_5', 5, 2);
      $table->decimal('percentage_score_4', 5, 2);
      $table->decimal('percentage_score_3', 5, 2);
      $table->decimal('percentage_score_2', 5, 2);
      $table->decimal('percentage_score_1', 5, 2);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('prodi_report');
  }
};
