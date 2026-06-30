<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarnetDelivery extends Model
{
    protected $table = 'carnet_deliveries';
    protected $fillable = ['carnet_request_id', 'recipient_name', 'delivery_date', 'delivery_location', 'signature_path', 'status'];

    public function request()
    {
        return $this->belongsTo(CarnetRequest::class, "carnet_request_id");
    }

}
