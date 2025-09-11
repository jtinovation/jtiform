<?php

namespace Database\Factories;

use App\Models\Answer;
use App\Models\QuestionOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AnswerOption>
 */
class AnswerOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            't_answer_id' => Answer::factory(),
            'm_question_option_id' => QuestionOption::factory()
        ];
    }
}
