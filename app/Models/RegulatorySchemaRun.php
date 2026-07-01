<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegulatorySchemaRun extends Model
{
    protected $fillable = [
        'run_number',
        'regulatory_schema_id',
        'period_id',
        'generated_by',
        'generated_at',
        'status',
        'total_records',
        'valid_records',
        'invalid_records',
        'file_name',
        'file_path',
        'checksum',
        'observations'
    ];

    public function schema()
    {
        return $this->belongsTo(RegulatorySchema::class, 'regulatory_schema_id');
    }

    public function period()
    {
        return $this->belongsTo(RegulatoryPeriod::class, 'period_id');
    }

    public function details()
    {
        return $this->hasMany(RegulatorySchemaRunDetail::class, 'regulatory_schema_run_id')->orderBy('line_number');
    }

    public function errors()
    {
        return $this->hasMany(RegulatorySchemaError::class, 'regulatory_schema_run_id');
    }

    public function submission()
    {
        return $this->hasOne(SimonMockSubmission::class, 'regulatory_schema_run_id');
    }
}
