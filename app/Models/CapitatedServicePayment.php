<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CapitatedServicePayment extends Model
{
    protected $table = 'capitated_service_payments';
    protected $fillable = ['capitated_contract_id', 'period', 'amount_paid', 'paid_at', 'payment_reference', 'status'];

    public function contract()
    {
        return $this->belongsTo(CapitatedServiceContract::class, "capitated_contract_id");
    }

}
