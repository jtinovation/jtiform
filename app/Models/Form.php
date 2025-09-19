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

    public function questions()
        {
            return $this->hasMany(Question::class,'m_form_id', 'id');
        }
    public function getRouteKeyName()
    {
        return 'id';
    }
    public function getIsActiveAttribute($value)
    {
        $now = now();

        // kalau ada start_at & end_at → cek realtime
        if ($this->start_at && $this->end_at) {
            return ($this->start_at <= $now && $this->end_at >= $now) ? 1 : 0;
        }

        // fallback ke nilai dari database
        return $value;
    }


}
