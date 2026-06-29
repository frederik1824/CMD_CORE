<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Novedad extends Model
{
    protected $table = 'novedades';

    protected $fillable = [
        'afiliado_type',
        'afiliado_id',
        'tipo_novedad_id',
        'campos_modificados',
        'estado',
        'motivo_estado',
        'lote_id',
        'creado_por',
        'fecha_novedad'
    ];

    protected $casts = [
        'fecha_novedad' => 'datetime',
        'campos_modificados' => 'array',
    ];

    public function tipoNovedad(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'tipo_novedad_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function getAfiliadoAttribute()
    {
        if ($this->afiliado_type === 'titular') {
            return Afiliado::find($this->afiliado_id);
        } elseif ($this->afiliado_type === 'dependiente') {
            return Dependiente::find($this->afiliado_id);
        }
        return null;
    }
}
