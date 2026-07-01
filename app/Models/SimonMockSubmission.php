<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimonMockSubmission extends Model
{
    protected $fillable = [
        'submission_number',
        'regulatory_schema_run_id',
        'regulatory_schema_id',
        'period_id',
        'submitted_by',
        'submitted_at',
        'status',
        'response_summary',
        'response_detail_json',
        'approved_at',
        'rejected_at',
        'rejection_reason'
    ];

    protected $casts = [
        'response_detail_json' => 'array'
    ];

    public function run()
    {
        return $this->belongsTo(RegulatorySchemaRun::class, 'regulatory_schema_run_id');
    }

    public function schema()
    {
        return $this->belongsTo(RegulatorySchema::class, 'regulatory_schema_id');
    }

    public function period()
    {
        return $this->belongsTo(RegulatoryPeriod::class, 'period_id');
    }

    public function logs()
    {
        return $this->hasMany(SimonMockSubmissionLog::class, 'simon_mock_submission_id')->orderBy('created_at', 'desc');
    }
}
