<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AutorizacionComentario extends Model {
    protected $table = 'autorizacion_comentarios';
    protected $fillable = ['autorizacion_id','user_id','comentario','es_interno'];
    public function usuario() { return $this->belongsTo(User::class, 'user_id'); }
    public function autorizacion() { return $this->belongsTo(Autorizacion::class, 'autorizacion_id'); }
}
