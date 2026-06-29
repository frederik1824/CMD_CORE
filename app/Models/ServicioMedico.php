<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicioMedico extends Model
{
    protected $table = 'servicios_medicos';

    protected $fillable = [
        'codigo',
        'descripcion',
        'cobertura_base',
        'es_alto_costo',
        'requiere_documento'
    ];

    protected $casts = [
        'cobertura_base' => 'decimal:2',
        'es_alto_costo' => 'boolean',
        'requiere_documento' => 'boolean',
    ];
}
