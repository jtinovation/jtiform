<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Carbon\Carbon;

class Form extends Model
{
  use HasFactory, SoftDeletes, HasUuids;

  protected $table = 'm_form';

  protected $fillable = [
    'code',
    'type',
    'cover_path',
    'cover_file',
    'title',
    'description',
    'is_active',
    'start_at',
    'end_at',
    'respondents'
  ];

  protected $casts = [
    'respondents' => 'array',
    'start_at' => 'datetime',
    'end_at'   => 'datetime',
  ];

  public function questions()
  {
    return $this->hasMany(Question::class, 'm_form_id', 'id');
  }

  public function submissions()
  {
    return $this->hasMany(Submission::class, 'm_form_id', 'id');
  }
}
