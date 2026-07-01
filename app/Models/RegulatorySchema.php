<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegulatorySchema extends Model
{
    protected $fillable = [
        'schema_code',
        'name',
        'description',
        'module_source',
        'report_type',
        'record_length',
        'periodicity',
        'simon_enabled',
        'status',
        'version',
        'effective_from',
        'effective_to',
        'documentation_file',
        'observations'
    ];

    public function fields()
    {
        return $this->hasMany(RegulatorySchemaField::class)->orderBy('order');
    }

    public function sections()
    {
        return $this->hasMany(RegulatorySchemaSection::class)->orderBy('order');
    }

    public function runs()
    {
        return $this->hasMany(RegulatorySchemaRun::class);
    }
}
