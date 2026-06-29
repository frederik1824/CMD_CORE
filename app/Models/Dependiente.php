<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dependiente extends Model
{
    protected $table = 'dependientes';

    protected $fillable = [
        'titular_id',
        'tipo_identificacion_id',
        'cedula',
        'nss',
        'nui',
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'sexo',
        'parentesco_id',
        'tipo_dependiente',
        'estudiante',
        'discapacitado',
        'nacionalidad',
        'requiere_documento',
        'estado_afiliacion',
        'motivo_estado'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'estudiante' => 'boolean',
        'discapacitado' => 'boolean',
        'requiere_documento' => 'boolean',
    ];

    public function titular(): BelongsTo
    {
        return $this->belongsTo(Afiliado::class, 'titular_id');
    }

    public function tipoIdentificacion(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'tipo_identificacion_id');
    }

    public function parentesco(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'parentesco_id');
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombres} {$this->apellidos}";
    }

    public function getEdadAttribute(): int
    {
        return $this->fecha_nacimiento ? $this->fecha_nacimiento->age : 0;
    }
}
