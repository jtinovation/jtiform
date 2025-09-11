<?php

namespace Database\Factories;

use App\Models\Form;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FormTargetRule>
 */
class FormTargetRuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'm_form_id' => Form::factory(),
            'mode' => 'MANUAL_LIST',
            'target_type' => fake()->name(),
            'filter_json' => json_encode([]),
        ];
    }
}
