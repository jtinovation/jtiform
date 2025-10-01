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
    Schema::create('m_question', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->string('question');
      $table->enum('type', ['text', 'checkbox', 'option']);
      $table->integer('sequence');
      $table->boolean('is_required');
      $table->foreignUuid('m_form_id')
        ->constrained('m_form')
        ->cascadeOnUpdate()
        ->cascadeOnDelete();
      $table->integer('old_db_id')->nullable();

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('m_question');
  }
};
