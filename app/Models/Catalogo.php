<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Catalogo extends Model
{
    protected $table = 'catalogos';
    protected $fillable = ['grupo', 'codigo', 'descripcion', 'activo'];

    public static function getByGrupo($grupo)
    {
        return self::where('grupo', $grupo)->where('activo', true)->get();
    }
}
