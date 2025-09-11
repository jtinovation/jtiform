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
            $table->string('target_type', 32);
            $table->uuid('target_id')->nullable();
            $table->uuid('relation_id')->nullable();
            $table->string('target_label', 256);
            $table->text('context_json');
            $table->timestamps();

            $table->unique(['t_submission_id', 'target_type', 'target_id', 'relation_id'], 'submission_target_unique_constraint');
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
