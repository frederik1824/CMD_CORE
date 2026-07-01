<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegulatorySchemaRunDetail extends Model
{
    protected $fillable = [
        'regulatory_schema_run_id',
        'source_model',
        'source_id',
        'record_type',
        'line_number',
        'raw_line',
        'validation_status',
        'errors_json',
        'warnings_json'
    ];

    protected $casts = [
        'errors_json' => 'array',
        'warnings_json' => 'array'
    ];

    public function run()
    {
        return $this->belongsTo(RegulatorySchemaRun::class, 'regulatory_schema_run_id');
    }
}
