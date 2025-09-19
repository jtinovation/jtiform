<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\AnswerOption;
use App\Models\Event;
use App\Models\EventItem;
use App\Models\Form;
use App\Models\FormTargetList;
use App\Models\FormTargetRule;
use App\Models\GalleryEvent;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Submission;
use App\Models\SubmissionTarget;
use App\Models\TEvent;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void {
    Form::factory(20)->create();
    FormTargetRule::factory(20)->create();
    FormTargetList::factory(30)->create();
    Question::factory(100)->create();
    QuestionOption::factory(80)->create();
    Submission::factory(80)->create();
    SubmissionTarget::factory(25)->create();
    Answer::factory(80)->create();
    AnswerOption::factory(40)->create();
  }
}
