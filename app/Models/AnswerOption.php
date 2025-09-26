<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnswerOption extends Model
{
    use HasFactory, HasUuids;

    protected $table = 't_answer_option';
    protected $fillable = [
      't_answer_id',
      'm_question_option_id',
    ];

    public function answer()
{
    return $this->belongsTo(Answer::class, 't_answer_id', 'id');
}

public function option()
{
    return $this->belongsTo(QuestionOption::class, 'm_question_option_id', 'id');
}

}
