<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;


class Report extends Model
{
  use HasUuids;
  protected $table = 'm_report';

  protected $fillable = [
    'id',
    'm_form_id',
    'm_user_id',
    'm_employee_id',
    'report_details',
    'overall_average_score',
    'predicate',
    'total_respondents',
  ];

  protected $casts = [
    'report_details' => 'array',
  ];

  public function form()
  {
    return $this->belongsTo(Form::class, 'm_form_id');
  }

  public function user()
  {
    return $this->belongsTo(User::class, 'm_user_id');
  }
}
