<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
  use HasFactory, HasUuids;

  protected $table = 't_submission';
  protected $fillable = [
    'm_form_id',
    'm_user_id',
    'submitted_at',
    'is_valid',
  ];

  public function form()
  {
    return $this->belongsTo(Form::class, 'm_form_id');
  }

  public function user()
  {
    return $this->belongsTo(User::class, 'm_user_id');
  }

  public function targets()
  {
    return $this->hasMany(SubmissionTarget::class, 't_submission_id');
  }

  public function target()
  {
    return $this->hasOne(SubmissionTarget::class, 't_submission_id');
  }
}
