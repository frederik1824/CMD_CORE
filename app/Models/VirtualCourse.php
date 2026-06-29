<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VirtualCourse extends Model
{
    protected $table = 'virtual_courses';

    protected $fillable = [
        'title',
        'description',
        'category',
        'hours',
        'image',
        'status'
    ];

    public function lessons(): HasMany
    {
        return $this->hasMany(VirtualLesson::class, 'course_id')->orderBy('order_index', 'asc');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(VirtualEnrollment::class, 'course_id');
    }

    public function assessment(): HasOne
    {
        return $this->hasOne(VirtualAssessment::class, 'course_id');
    }
}
