<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\SubmissionTarget;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Answer>
 */
class AnswerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $questionOptionId = null;
        if(fake()->boolean(33)){
          $questionOptionId = QuestionOption:: pluck('id')->random();
        }

        return [
            't_submission_target_id' => SubmissionTarget::pluck('id')->random(),
            'm_question_id' => Question::pluck('id')->random(),
            'text_value' => $questionOptionId && fake()->boolean(33) ? fake()->text() : null,
            'm_question_option_id' => $questionOptionId,
            'score' => fake()->randomFloat(1, 0, 100),
            'checked_at' => fake()->dateTimeBetween('now', '+30 days')
        ];
    }
}
