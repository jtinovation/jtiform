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
        Schema::create('m_form_target_rule', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('m_form_id')->constrained('m_form')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('mode', 16);
            $table->string('target_type', 32);
            $table->text('filter_json');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_form_target_rule');
    }
};
