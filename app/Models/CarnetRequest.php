<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarnetRequest extends Model
{
    protected $table = 'carnet_requests';
    protected $fillable = ['affiliate_id', 'affiliate_type', 'request_type', 'printing_center_id', 'request_date', 'print_date', 'batch_number', 'status'];

    public function printingCenter()
    {
        return $this->belongsTo(PrintingCenter::class, "printing_center_id");
    }

    public function deliveries()
    {
        return $this->hasMany(CarnetDelivery::class, "carnet_request_id");
    }

    public function transfers()
    {
        return $this->hasMany(CarnetTransfer::class, "carnet_request_id");
    }

    public function getAffiliateAttribute()
    {
        if ($this->affiliate_type === "dependiente") {
            return Dependiente::find($this->affiliate_id);
        }
        return Afiliado::find($this->affiliate_id);
    }

}
