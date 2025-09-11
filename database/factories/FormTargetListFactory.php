<?php

namespace Database\Factories;

use App\Models\FormTargetRule;
use Faker\Guesser\Name;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FormTargetList>
 */
class FormTargetListFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $targetType = fake()->randomElement(['mk_dosen', 'dosen', 'laboran', 'teknisi', 'unit', 'fasilitas']);

        return [
            'm_form_target_rule_id' => FormTargetRule::factory(),
            'target_type' => $targetType,
            'target_id' => fake()->uuid(),
            'relation_id' => null,
            'target_label' => fake()->name()
        ];
    }
}
