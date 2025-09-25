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
        $startTime = $this->faker->dateTimeBetween('now', '+7 days');
        $startTimeCarbon = Carbon::instance($startTime);
        $randomMinutes = mt_rand(60, 120);
        $endTime = $startTimeCarbon->addMinutes($randomMinutes);

        $isAnonymous = fake()->boolean();

        return [
            'm_form_id' => Form::pluck('id')->random(),
            'm_user_id' =>  $isAnonymous ? null : User::pluck('id')->random(),
            'started_at' => $startTime,
            'submitted_at' => $endTime,
            'status' => fake()->randomElement(['in_progress', 'submitted', 'invalidated']),
            'is_anonymous' =>  $isAnonymous,
            'is_valid' => fake()->boolean(70),
            'meta_json' => json_encode(['tr_lecturer_subject_id' => fake()->uuid()])
        ];
    }
}
