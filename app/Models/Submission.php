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
      'started_at',
      'submitted_at',
      'status',
      'is_anonymous',
      'is_valid',
      'meta_json'
    ];
}
