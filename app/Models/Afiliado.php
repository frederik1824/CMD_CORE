<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Afiliado extends Model
{
    protected $table = 'afiliados';

    protected $fillable = [
        'tipo_identificacion_id',
        'contract_number_id',
        'contract_number',
        'contract_range_id',
        'cedula',
        'nss',
        'nui',
        'nombres',
        'primer_apellido',
        'segundo_apellido',
        'fecha_nacimiento',
        'sexo',
        'provincia',
        'municipio',
        'sector',
        'direccion',
        'telefono',
        'correo',
        'numero_contrato',
        'fecha_suscripcion',
        'estado_afiliacion',
        'esta_carnetizado',
        'tiene_formulario',
        'ubicacion_formulario',
        'motivo_estado',
        'activo_nomina',
        'tiene_aporte',
        'regimen_actual',
        'entidad_actual',
        'tipo_afiliacion',
        'fecha_afiliacion',
        'ultimo_periodo_pagado'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_suscripcion' => 'date',
        'fecha_afiliacion' => 'date',
        'activo_nomina' => 'boolean',
        'tiene_aporte' => 'boolean',
        'esta_carnetizado' => 'boolean',
        'tiene_formulario' => 'boolean',
    ];

    public function tipoIdentificacion(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'tipo_identificacion_id');
    }

    public function dependientes(): HasMany
    {
        return $this->hasMany(Dependiente::class, 'titular_id');
    }

    public function capitationNotifications(): HasMany
    {
        return $this->hasMany(CapitationNotification::class, 'afiliado_id');
    }

    public function dispersionCutDetails(): HasMany
    {
        return $this->hasMany(DispersionCutDetail::class, 'afiliado_id');
    }

    public function claims(): HasMany
    {
        return $this->hasMany(AuthorizationClaim::class, 'afiliado_id');
    }

    public function contractNumber(): BelongsTo
    {
        return $this->belongsTo(AffiliationContractNumber::class, 'contract_number_id');
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombres} {$this->primer_apellido} {$this->segundo_apellido}");
    }

    public function getEdadAttribute(): int
    {
        return $this->fecha_nacimiento ? $this->fecha_nacimiento->age : 0;
    }

    public function solicitudesServicio(): HasMany
    {
        return $this->hasMany(SolicitudServicio::class, 'afiliado_id');
    }
}
