<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pss extends Model
{
    protected $table = 'pss';

    protected $fillable = [
        'rnc',
        'nombre',
        'tipo_entidad',
        'telefono',
        'correo',
        'direccion',
        'estado',
        'nivel_atencion',
        'tipo_pss',
        'red_contratada',
        'contrato_vigente',
        'commercial_name',
        'habilitation_number',
        'pss_type',
        'pss_category',
        'level_of_care',
        'network_status',
        'contract_status',
        'province',
        'municipality',
    ];

    public function contratos(): HasMany
    {
        return $this->hasMany(ContratoPss::class, 'pss_id');
    }

    public function serviceContracts(): HasMany
    {
        return $this->hasMany(PssServiceContract::class, 'pss_id');
    }

    public function getContratoActivoAttribute()
    {
        return $this->contratos()
            ->where('estado', 'Activo')
            ->where('fecha_inicio', '<=', now())
            ->where('fecha_fin', '>=', now())
            ->first();
    }

    // Accessors and Mutators for English aliases compatibility
    public function getNameAttribute()
    {
        return $this->nombre;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['nombre'] = $value;
    }

    public function getPhoneAttribute()
    {
        return $this->telefono;
    }

    public function setPhoneAttribute($value)
    {
        $this->attributes['telefono'] = $value;
    }

    public function getEmailAttribute()
    {
        return $this->correo;
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['correo'] = $value;
    }

    public function getAddressAttribute()
    {
        return $this->direccion;
    }

    public function setAddressAttribute($value)
    {
        $this->attributes['direccion'] = $value;
    }

    public function getStatusAttribute()
    {
        return $this->estado;
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['estado'] = $value;
    }

    public function getLevelOfCareAttribute()
    {
        return $this->nivel_atencion;
    }

    public function setLevelOfCareAttribute($value)
    {
        $this->attributes['nivel_atencion'] = $value;
    }

    public function getNetworkStatusAttribute()
    {
        return $this->red_contratada;
    }

    public function setNetworkStatusAttribute($value)
    {
        $this->attributes['red_contratada'] = $value;
    }

    public function getContractStatusAttribute()
    {
        return $this->contrato_vigente;
    }

    public function setContractStatusAttribute($value)
    {
        $this->attributes['contrato_vigente'] = $value;
    }
}
