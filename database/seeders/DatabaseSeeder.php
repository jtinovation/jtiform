<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\AnswerOption;
use App\Models\Form;
use App\Models\FormTargetList;
use App\Models\FormTargetRule;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Submission;
use App\Models\SubmissionTarget;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    // $this->call([
    //   UserSeeder::class,
    // ]);

    // Form::factory(20)->create();
    // FormTargetRule::factory(20)->create();
    // FormTargetList::factory(30)->create();
    // Question::factory(100)->create();
    // QuestionOption::factory(80)->create();
    // Submission::factory(80)->create();
    // SubmissionTarget::factory(25)->create();
    // Answer::factory(80)->create();
    // AnswerOption::factory(40)->create();

    // $this->call([
    //   TestDataSeeder::class
    // ]);

    // $this->call([
    //   FormSeeder::class
    // ]);


    $form = Form::where('type', 'general')->first();
    if ($form) {
      $questions = Question::where('m_form_id', $form->id)->with('options')->get();
      $submissions = Submission::factory(50)->create([
        'm_form_id' => $form->id,
      ])->each(function ($submission) {
        $submission->target()->create([
          'target_type' => 'general',
        ]);
      });

      foreach ($submissions as $submission) {
        $question = $questions->random();
        $answer = Answer::create([
          't_submission_target_id' => $submission->target->id,
          'm_question_id' => $question->id,
          'text_value' => $question->type === 'text' ? fake()->sentence() : null,
          'm_question_option_id' => $question->type === 'option' && $question->options ? $question->options->random()->id : null,
          'score' => 0,
          'checked_at' => now(),
        ]);

        if ($question->type === 'checkbox' && $question->options) {
          $option = $question->options->random();
          AnswerOption::create([
            't_answer_id' => $answer->id,
            'm_question_option_id' => $option->id,
          ]);
        }
      }
    }
  }
}
