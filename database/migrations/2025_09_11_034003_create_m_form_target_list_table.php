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
        Schema::create('m_form_target_list', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('m_form_target_rule_id')
            ->constrained('m_form_target_rule')
            ->cascadeOnUpdate()
            ->cascadeOnDelete();
            $table->string('target_type', 32);
            $table->uuid('target_id');
            $table->uuid('relation_id');
            $table->string('target_label', 256);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_form_target_list');
    }
};
