<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegulatorySchemaField extends Model
{
    protected $fillable = [
        'regulatory_schema_id',
        'section_type',
        'field_name',
        'field_label',
        'data_type',
        'length',
        'required',
        'start_position',
        'end_position',
        'default_value',
        'constant_value',
        'padding',
        'padding_character',
        'format_mask',
        'catalog_code',
        'validation_rule',
        'source_model',
        'source_field',
        'transformation_rule',
        'order',
        'is_active'
    ];

    public function schema()
    {
        return $this->belongsTo(RegulatorySchema::class, 'regulatory_schema_id');
    }
}
