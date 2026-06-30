<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarnetAdjustment extends Model
{
    protected $table = 'carnet_adjustments';
    protected $fillable = ['supply_id', 'printing_center_id', 'adjustment_type', 'quantity', 'reason', 'user_id'];

    public function supply()
    {
        return $this->belongsTo(PrintingSupply::class, "supply_id");
    }

    public function printingCenter()
    {
        return $this->belongsTo(PrintingCenter::class, "printing_center_id");
    }

}
