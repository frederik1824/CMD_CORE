<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VirtualAssessment extends Model
{
    protected $table = 'virtual_assessments';

    protected $fillable = [
        'course_id',
        'title',
        'questions_json',
        'min_score'
    ];

    protected $casts = [
        'questions_json' => 'array'
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(VirtualCourse::class, 'course_id');
    }
}
