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
        $targetType = fake()->randomElement(['mk_dosen', 'dosen', 'laboran', 'teknisi', 'unit', 'fasilitas']);

        return [
            't_submission_id' => Submission::factory(),
            'target_type' => $targetType,
            'target_id' => fake()->uuid(),
            'relation_id' => null,
            'target_label' => fake()->name(),
            'context_json' => json_encode([])
        ];
    }
}
