<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $table = 'documentos';

    protected $fillable = [
        'entidad_type',
        'entidad_id',
        'nombre_archivo',
        'ruta_archivo',
        'tipo_documento',
        'fecha_carga'
    ];

    protected $casts = [
        'fecha_carga' => 'datetime',
    ];

    public function getEntidadAttribute()
    {
        if ($this->entidad_type === 'titular') {
            return Afiliado::find($this->entidad_id);
        } elseif ($this->entidad_type === 'dependiente') {
            return Dependiente::find($this->entidad_id);
        } elseif ($this->entidad_type === 'autorizacion') {
            return Autorizacion::find($this->entidad_id);
        }
        return null;
    }
}
