<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'm_question';
    protected $fillable = [
      'question',
      'type',
      'sequence',
      'is_required',
      'm_form_id'
    ];
    
    public function form()
{
    return $this->belongsTo(Form::class, 'm_form_id', 'id');
}

public function options()
{
    return $this->hasMany(QuestionOption::class, 'm_question_id', 'id')
                ->orderBy('sequence');
}

public function answers()
{
    return $this->hasMany(Answer::class, 'm_question_id', 'id');
}

}
