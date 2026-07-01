<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegulatoryCatalogItem extends Model
{
    protected $fillable = [
        'regulatory_catalog_id',
        'item_code',
        'item_description',
        'extra_data',
        'status'
    ];

    public function catalog()
    {
        return $this->belongsTo(RegulatoryCatalog::class, 'regulatory_catalog_id');
    }
}
