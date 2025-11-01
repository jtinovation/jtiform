<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
  use HasFactory, HasUuids;

  protected $table = 'm_question_option';
  protected $fillable = [
    'm_question_id',
    'answer',
    'sequence',
    'point'
  ];

  public function question()
  {
    return $this->belongsTo(Question::class, 'm_question_id', 'id');
  }

  public function answers()
  {
    return $this->hasMany(Answer::class, 'm_question_option_id', 'id');
  }
}
