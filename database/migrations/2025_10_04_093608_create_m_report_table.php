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
    Schema::create('m_report', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->foreignUuid('m_form_id')->constrained('m_form')->onDelete('cascade')->onUpdate('cascade');
      $table->foreignUuid('m_user_id')->constrained('m_user')->onDelete('cascade')->onUpdate('cascade');
      $table->uuid('m_employee_id')->index()->nullable();
      $table->uuid('m_major_id_employee')->index()->nullable();
      $table->uuid('m_study_program_id_employee')->index()->nullable();
      $table->json('report_details'); // To store scores for each course and question
      $table->decimal('overall_average_score', 5, 2); // Corresponds to 'Rata-Rata Nilai Keseluruhan Dosen (NKD)'
      $table->string('predicate'); // Corresponds to the 'Predikat' (e.g., 'Sangat Baik')
      $table->unsignedInteger('total_respondents'); // Total number of students who responded across all courses
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('m_report');
  }
};

// example report_details structure
// [
//     {
//         "course_code": "TIF160705",
//         "course_name": "Workshop Developer Operational",
//         "class": "TIF-BWS",
//         "respondents": 11,
//         "average_score": 88.36,
//         "study_program_id_subject": "uuid-of-study-program-subject",
//         "major_id_subject": "uuid-of-major-subject",
//         "subject_semester_id": "uuid-of-subject-semester",
//         "semester_id": "uuid-of-semester",
//         "scores": [
//             {"question_id": 1, "score": 90.91},
//             {"question_id": 2, "score": 90.91},
//             ...
//         ]
//     },
//     {
//         "course_code": "MIF160702",
//         "course_name": "Manajemen Proyek Sistem Informasi",
//         "class": "MIF",
//         "respondents": 49,
//         "average_score": 89.60,
//         "study_program_id_subject": "uuid-of-study-program-subject",
//         "major_id_subject": "uuid-of-major-subject",
//         "subject_semester_id": "uuid-of-subject-semester",
//         "semester_id": "uuid-of-semester",
//         "scores": [
//             {"question_id": 1, "score": 90.20},
//             {"question_id": 2, "score": 89.39},
//             ...
//         ]
//     }
// ]
