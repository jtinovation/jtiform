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
    Schema::table('m_form', function (Blueprint $table) {
      $table->uuid('created_by')->after('old_db_id')->nullable();
      $table->foreign('created_by')->references('id')->on('m_user')->onDelete('SET NULL');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('m_form', function (Blueprint $table) {
      $table->dropForeign(['created_by']);
      $table->dropColumn('created_by');
    });
  }
};
