<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VirtualProgress extends Model
{
    protected $table = 'virtual_progress';

    protected $fillable = [
        'enrollment_id',
        'lesson_id',
        'completed',
        'completed_at'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'completed' => 'boolean'
    ];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(VirtualEnrollment::class, 'enrollment_id');
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(VirtualLesson::class, 'lesson_id');
    }
}
