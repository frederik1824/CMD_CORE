<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrintingSupply extends Model
{
    protected $table = 'printing_supplies';
    protected $fillable = ['name', 'supply_family', 'initial_stock', 'current_stock', 'unit'];

}
