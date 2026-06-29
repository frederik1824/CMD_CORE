<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AutorizacionEstadoLog extends Model {
    protected $table = 'autorizacion_estado_logs';
    protected $fillable = ['autorizacion_id','user_id','estado_anterior','estado_nuevo','motivo','ip_address'];
    public function usuario() { return $this->belongsTo(User::class, 'user_id'); }
    public function autorizacion() { return $this->belongsTo(Autorizacion::class, 'autorizacion_id'); }
}
