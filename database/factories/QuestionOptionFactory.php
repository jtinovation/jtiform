<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuestionOption>
 */
class QuestionOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'm_question_id' => Question::pluck('id')->random(),
            'answer' => fake()->text(),
            'sequence' => fake()->randomNumber(1, 10),
            'point' => fake()->numberBetween(0, 100)
          ];
    }
}
