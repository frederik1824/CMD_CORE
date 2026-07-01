<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegulatorySchemaSection extends Model
{
    protected $fillable = [
        'regulatory_schema_id',
        'section_type',
        'name',
        'record_type_constant',
        'order'
    ];

    public function schema()
    {
        return $this->belongsTo(RegulatorySchema::class, 'regulatory_schema_id');
    }
}
