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
        Schema::create('t_answer', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('t_submission_target_id')
            ->constrained('t_submission_target')
            ->cascadeOnUpdate()
            ->cascadeOnDelete();
            $table->foreignUuid('m_question_id')
            ->constrained('m_question')
            ->cascadeOnUpdate()
            ->cascadeOnDelete();
            $table->text('text_value')->nullable();
            $table->foreignUuid('m_question_option_id')
            ->nullable()
            ->constrained('m_question_option')
            ->cascadeOnUpdate()
            ->cascadeOnDelete();
            $table->decimal('score');
            $table->dateTime('checked_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_answer');
    }
};
