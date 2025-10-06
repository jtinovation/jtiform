<?php

use App\Enums\FormTypeEnum;
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
      $table->enum('type', FormTypeEnum::toArray());
      $table->string('cover_path')->nullable();
      $table->string('cover_file')->nullable();
      $table->string('title');
      $table->text('description')->nullable();
      $table->boolean('is_active')->default(true);
      $table->dateTime('start_at');
      $table->dateTime('end_at');
      $table->text('respondents')->nullable();
      $table->uuid('session_id')->nullable();
      $table->boolean('is_even')->nullable();
      $table->integer('old_db_id')->nullable();
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
