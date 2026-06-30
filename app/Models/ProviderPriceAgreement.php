<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderPriceAgreement extends Model
{
    protected $table = 'provider_price_agreements';
    protected $fillable = ['pss_id', 'health_plan_id', 'servicio_medico_id', 'price', 'status'];

    public function pss()
    {
        return $this->belongsTo(Pss::class, "pss_id");
    }

    public function plan()
    {
        return $this->belongsTo(HealthPlan::class, "health_plan_id");
    }

    public function service()
    {
        return $this->belongsTo(ServicioMedico::class, "servicio_medico_id");
    }

}
