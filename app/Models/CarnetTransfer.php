<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarnetTransfer extends Model
{
    protected $table = 'carnet_transfers';
    protected $fillable = ['carnet_request_id', 'origin_location', 'destination_location', 'sent_date', 'received_date', 'status'];

    public function request()
    {
        return $this->belongsTo(CarnetRequest::class, "carnet_request_id");
    }

}
