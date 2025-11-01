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
    Schema::table('m_user', function (Blueprint $table) {
      $table->uuid('major_id')->nullable()->after('permissions');
      $table->uuid('study_program_id')->nullable()->after('major_id');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('m_user', function (Blueprint $table) {
      $table->dropColumn('major_id');
      $table->dropColumn('study_program_id');
    });
  }
};
