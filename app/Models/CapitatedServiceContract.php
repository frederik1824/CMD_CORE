<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CapitatedServiceContract extends Model
{
    protected $table = 'capitated_service_contracts';
    protected $fillable = ['pss_id', 'contract_number', 'coverage_population_count', 'monthly_capitation_rate', 'total_monthly_amount', 'status', 'start_date', 'end_date'];

    public function pss()
    {
        return $this->belongsTo(Pss::class, "pss_id");
    }

    public function payments()
    {
        return $this->hasMany(CapitatedServicePayment::class, "capitated_contract_id");
    }

}
