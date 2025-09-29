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
    Schema::create('t_submission', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->foreignUuid('m_form_id')
        ->constrained('m_form')
        ->cascadeOnUpdate()
        ->cascadeOnDelete();
      $table->foreignUuid('m_user_id')->nullable()
        ->constrained('m_user')
        ->cascadeOnUpdate()
        ->cascadeOnDelete();
      $table->dateTime('submitted_at')->nullable();
      $table->boolean('is_valid')->default(false);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('t_submission');
  }
};
