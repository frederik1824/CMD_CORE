<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TarifaPss extends Model
{
    protected $table = 'tarifas_pss';

    protected $fillable = [
        'contrato_pss_id',
        'servicio_medico_id',
        'monto_tarifa'
    ];

    protected $casts = [
        'monto_tarifa' => 'decimal:2',
    ];

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(ContratoPss::class, 'contrato_pss_id');
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(ServicioMedico::class, 'servicio_medico_id');
    }
}
