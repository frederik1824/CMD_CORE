<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VirtualLesson extends Model
{
    protected $table = 'virtual_lessons';

    protected $fillable = [
        'course_id',
        'title',
        'duration_minutes',
        'content',
        'order_index',
        'status'
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(VirtualCourse::class, 'course_id');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(VirtualMaterial::class, 'lesson_id');
    }
}
