<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Form>
 */
class FormFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'code' => fake()->unique()->bothify('??##??##'),
      'type' => fake()->randomElement(['questionnaire', 'survey']),
      'cover_path' => 'images/covers',
      'cover_file' => fake()->numerify('cover-#####.jpg'),
      'title' => fake()->sentence(5),
      'description' => fake()->paragraphs(3, true),
      'is_active' => fake()->boolean(),
      'start_at' => fake()->dateTimeBetween('-60 days', 'now'),
      'end_at' => fake()->dateTimeBetween('now', '+60 days')
    ];
  }
}
