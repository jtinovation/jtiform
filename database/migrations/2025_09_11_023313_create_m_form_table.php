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
        Schema::create('m_form', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code');
            $table->string('cover_path')->nullable();
            $table->string('cover_file')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_active');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_form');
    }
};
