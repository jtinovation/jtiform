<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
  use HasFactory, HasUuids;

  protected $table = 't_answer';
  protected $fillable = [
    't_submission_target_id',
    'm_question_id',
    'text_value',
    'm_question_option_id',
    'score',
    'checked_at'
  ];

  public function answerOptions()
  {
    return $this->hasMany(AnswerOption::class, 't_answer_id', 'id');
  }

  public function question()
  {
    return $this->belongsTo(Question::class, 'm_question_id', 'id');
  }

  public function questionOption()
  {
    return $this->belongsTo(QuestionOption::class, 'm_question_option_id', 'id');
  }
}
