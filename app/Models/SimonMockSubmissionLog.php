<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimonMockSubmissionLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'simon_mock_submission_id',
        'event_type',
        'old_status',
        'new_status',
        'message',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function submission()
    {
        return $this->belongsTo(SimonMockSubmission::class, 'simon_mock_submission_id');
    }
}
