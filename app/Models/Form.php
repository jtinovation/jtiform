<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;

class Form extends Model
{
  use HasFactory, SoftDeletes, HasUuids;

  protected $table = 'm_form';
  protected $fillable = [
    'code',
    'form_type',
    'cover_path',
    'cover_file',
    'title',
    'description',
    'is_active',
    'start_at',
    'end_at',
  ];
  protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
    ];

    /**
     * 🔹 Accessor: hitung status aktif berdasarkan tanggal
     */
    public function getIsCurrentlyActiveAttribute()
    {
        $now = Carbon::now();
        return $this->start_at && $this->end_at
            ? $now->between($this->start_at, $this->end_at)
            : false;
    }
}
