<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReglaAutorizacion extends Model
{
    protected $table = 'reglas_autorizacion';

    protected $fillable = [
        'codigo',
        'descripcion',
        'tipo_regla',
        'valor',
        'estado'
    ];
}
