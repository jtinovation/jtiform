<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormTargetList extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'm_form_target_list';
    protected $fillable = [
      'm_form_target_rule_id',
      'target_type',
      'target_id',
      'relation_id',
      'target_label'
    ];
}
