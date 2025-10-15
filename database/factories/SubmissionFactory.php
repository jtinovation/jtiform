<?php

namespace Database\Factories;

use App\Models\Form;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Submission>
 */
class SubmissionFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {


    return [
      'm_form_id' => Form::pluck('id')->random(),
      'm_user_id' =>   User::pluck('id')->random(),
      'submitted_at' => now(),
      'is_valid' => true,
    ];
  }
}
