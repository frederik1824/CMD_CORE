<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderContractedService extends Model
{
    protected $table = 'provider_contracted_services';
    protected $fillable = ['pss_id', 'servicio_medico_id', 'status'];

    public function pss()
    {
        return $this->belongsTo(Pss::class, "pss_id");
    }

    public function service()
    {
        return $this->belongsTo(ServicioMedico::class, "servicio_medico_id");
    }

}
