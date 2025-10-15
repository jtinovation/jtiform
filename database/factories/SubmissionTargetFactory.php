<?php

namespace Database\Factories;

use App\Models\Submission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubmissionTarget>
 */
class SubmissionTargetFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      't_submission_id' => Submission::pluck('id')->random(),
      'target_type' => 'general',
      'target_id' => null,
    ];
  }
}
