<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegulatoryCatalog extends Model
{
    protected $fillable = [
        'catalog_code',
        'name',
        'description',
        'status'
    ];

    public function items()
    {
        return $this->hasMany(RegulatoryCatalogItem::class)->orderBy('item_code');
    }
}
