<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionTarget extends Model
{
  use HasFactory, HasUuids;

  protected $table = 't_submission_target';
  protected $fillable = [
    't_submission_id',
    'target_type',
    'target_id',
  ];
}
