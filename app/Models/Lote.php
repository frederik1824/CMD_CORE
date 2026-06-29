<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lote extends Model
{
    protected $table = 'lotes';

    protected $fillable = [
        'numero_lote',
        'tipo_lote',
        'estado_lote',
        'total_registros',
        'registros_ok',
        'registros_re',
        'creado_por',
        'fecha_creacion',
        'fecha_procesamiento'
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'fecha_procesamiento' => 'datetime',
    ];

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(LoteDetalle::class, 'lote_id');
    }
}
