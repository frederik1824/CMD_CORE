<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutorizacionDetalle extends Model
{
    protected $table = 'autorizacion_detalles';

    protected $fillable = [
        'autorizacion_id',
        'codigo',
        'descripcion',
        'cantidad',
        'monto',
        'estado'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    public function autorizacion(): BelongsTo
    {
        return $this->belongsTo(Autorizacion::class, 'autorizacion_id');
    }
}
