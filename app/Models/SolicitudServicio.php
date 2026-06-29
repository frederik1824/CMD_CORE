<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudServicio extends Model
{
    protected $table = 'solicitud_servicios';

    protected $fillable = [
        'afiliado_id',
        'tipo_solicitud',
        'descripcion',
        'soporte_path',
        'estado'
    ];

    public function afiliado()
    {
        return $this->belongsTo(Afiliado::class, 'afiliado_id');
    }
}
