<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormTargetRule extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'm_form_target_rule';
    protected $fillable = [
      'm_form_id',
      'mode',
      'target_type',
      'filter_json',
    ];
}
