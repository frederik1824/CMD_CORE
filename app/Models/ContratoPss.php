<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContratoPss extends Model
{
    protected $table = 'contratos_pss';

    protected $fillable = [
        'pss_id',
        'numero_contrato',
        'fecha_inicio',
        'fecha_fin',
        'estado'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function pss(): BelongsTo
    {
        return $this->belongsTo(Pss::class, 'pss_id');
    }

    public function tarifas(): HasMany
    {
        return $this->hasMany(TarifaPss::class, 'contrato_pss_id');
    }
}
