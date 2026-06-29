<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class Bitacora extends Model
{
    protected $table = 'bitacoras';

    protected $fillable = [
        'user_id',
        'accion',
        'modulo',
        'detalles',
        'ip_address',
        'fecha_registro'
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
        'detalles' => 'array',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function registrar(string $modulo, string $accion, $detalles = null): self
    {
        return retry(5, function() use ($modulo, $accion, $detalles) {
            return self::create([
                'user_id' => Auth::id(),
                'accion' => $accion,
                'modulo' => $modulo,
                'detalles' => is_array($detalles) ? $detalles : ['mensaje' => $detalles],
                'ip_address' => Request::ip(),
                'fecha_registro' => now()
            ]);
        }, 100);
    }
}
