<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrintingSupplyMovement extends Model
{
    protected $table = 'printing_supply_movements';
    protected $fillable = ['supply_id', 'printing_center_id', 'movement_type', 'quantity', 'reason', 'user_id'];

    public function supply()
    {
        return $this->belongsTo(PrintingSupply::class, "supply_id");
    }

    public function printingCenter()
    {
        return $this->belongsTo(PrintingCenter::class, "printing_center_id");
    }

}
