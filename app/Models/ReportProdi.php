<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ReportProdi extends Model
{
  use HasUuids;
  protected $table = 'prodi_report';

  protected $fillable = [
    'id',
    'm_study_program_id',
    'm_major_id',
    'm_session_id',
    'is_even',
    'm_question_id',
    'total_respondents',
    'percentage_score_5',
    'percentage_score_4',
    'percentage_score_3',
    'percentage_score_2',
    'percentage_score_1',
  ];
}
