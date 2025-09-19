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
    Schema::create('m_user', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->uuid('external_id')->nullable();
      $table->string('name');
      $table->string('email');
      $table->text('token')->nullable();
      $table->text('roles')->nullable();
      $table->text('permissions')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('m_user');
  }
};
