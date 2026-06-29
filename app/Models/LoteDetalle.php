<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoteDetalle extends Model
{
    protected $table = 'lote_detalles';

    protected $fillable = [
        'lote_id',
        'entidad_type',
        'entidad_id',
        'estado',
        'motivo_rechazo'
    ];

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function getEntidadAttribute()
    {
        if ($this->entidad_type === 'titular') {
            return Afiliado::find($this->entidad_id);
        } elseif ($this->entidad_type === 'dependiente') {
            return Dependiente::find($this->entidad_id);
        } elseif ($this->entidad_type === 'novedad') {
            return Novedad::find($this->entidad_id);
        }
        return null;
    }
}
