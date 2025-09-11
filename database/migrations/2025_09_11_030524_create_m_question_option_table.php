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
        Schema::create('m_question_option', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('m_question_id')
            ->constrained('m_question')
            ->cascadeOnUpdate()
            ->cascadeOnDelete();
            $table->text('answer');
            $table->integer('sequence');
            $table->integer('point');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_question_option');
    }
};
