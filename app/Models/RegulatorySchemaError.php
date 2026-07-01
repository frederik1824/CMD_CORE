<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegulatorySchemaError extends Model
{
    protected $fillable = [
        'regulatory_schema_run_id',
        'detail_id',
        'field_name',
        'error_code',
        'error_message',
        'severity',
        'expected_value',
        'current_value',
        'position'
    ];

    public function run()
    {
        return $this->belongsTo(RegulatorySchemaRun::class, 'regulatory_schema_run_id');
    }

    public function detail()
    {
        return $this->belongsTo(RegulatorySchemaRunDetail::class, 'detail_id');
    }
}
