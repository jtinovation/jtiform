<?php

namespace Database\Factories;

use App\Models\Form;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question' => fake()->sentence() . '?',
            'type' => fake()->randomElement(['text', 'option', 'checkbox']),
            'sequence' => fake()->numberBetween(1, 10),
            'is_required' => fake()->boolean(),
            'm_form_id' => Form::pluck('id')->random()
        ];
    }
}
