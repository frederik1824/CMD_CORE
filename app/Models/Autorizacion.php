<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Autorizacion extends Model
{
    protected $table = 'autorizaciones';

    protected $fillable = [
        'numero_autorizacion',
        'afiliado_type',
        'afiliado_id',
        'pss_id',
        'medico_solicitante',
        'diagnostico',
        'servicio_medico_id',
        'procedimiento',
        'monto_solicitado',
        'monto_contratado',
        'prioridad',
        'estado',
        'motivo_estado',
        'fecha_solicitud',
        'fecha_respuesta',
        'usuario_responsable_id',
        'canal_recepcion',
        'persona_contacto',
        'telefono_contacto',
        'codigo_diagnostico',
        'tipo_servicio',
        'especialidad',
        'auditor_id',
        'representante_id',
        'tipo_afiliado_display',
        'codigo_respuesta',
        'pdss_service_id',
        'simon_code_snapshot',
        'cups_code_snapshot',
        'service_description_snapshot',
        'coverage_type_snapshot',
        'pdss_group_snapshot',
        'pdss_subgroup_snapshot',
        'level_requested',
        'coverage_allowed',
        'copay_type_snapshot',
        'amount_coverage_snapshot',
        'monto_ars',
        'monto_afiliado',
        'copago',
        'exceso',
        'monto_no_cubierto',
        'exception_coverage_type',
        // Columnas Contratación V2 Snapshots
        'origin',
        'channel',
        'pss_contract_id',
        'pss_contract_version_id',
        'pss_tariff_schedule_id',
        'pss_tariff_item_id',
        'contracted_amount_snapshot',
        'affiliate_copay_amount',
        'ars_amount',
        'non_covered_amount',
        'claim_status',
        'claimed_at',
        'claim_id',
        'internal_notes',
    ];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'fecha_respuesta' => 'datetime',
        'monto_solicitado' => 'decimal:2',
        'monto_contratado' => 'decimal:2',
        'monto_ars' => 'decimal:2',
        'monto_afiliado' => 'decimal:2',
        'copago' => 'decimal:2',
        'exceso' => 'decimal:2',
        'monto_no_cubierto' => 'decimal:2',
        'level_requested' => 'integer',
    ];

    public function pss(): BelongsTo
    {
        return $this->belongsTo(Pss::class, 'pss_id');
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(ServicioMedico::class, 'servicio_medico_id');
    }

    public function usuarioResponsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_responsable_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(AutorizacionDetalle::class, 'autorizacion_id');
    }

    public function comentarios(): HasMany
    {
        return $this->hasMany(AutorizacionComentario::class, 'autorizacion_id')->orderBy('created_at','asc');
    }

    public function estadoLogs(): HasMany
    {
        return $this->hasMany(AutorizacionEstadoLog::class, 'autorizacion_id')->orderBy('created_at','asc');
    }

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    public function representante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'representante_id');
    }

    public function getTiempoRespuestaAttribute()
    {
        if (!$this->fecha_respuesta) return null;
        $diff = $this->fecha_solicitud->diffInMinutes($this->fecha_respuesta);
        if ($diff < 60) return $diff . ' min';
        return round($diff/60, 1) . ' hrs';
    }

    public function servicioPdss(): BelongsTo
    {
        return $this->belongsTo(PdssService::class, 'pdss_service_id');
    }

    public function claims(): HasMany
    {
        return $this->hasMany(AuthorizationClaim::class, 'authorization_id');
    }

    public function payables(): HasMany
    {
        return $this->hasMany(AccountPayable::class, 'authorization_id');
    }

    public function timelineEvents(): HasMany
    {
        return $this->hasMany(AuthorizationTimelineEvent::class, 'authorization_id')->orderBy('created_at', 'asc');
    }

    public function getAfiliadoAttribute()
    {
        if ($this->afiliado_type === 'titular') {
            return Afiliado::find($this->afiliado_id);
        } elseif ($this->afiliado_type === 'dependiente') {
            return Dependiente::find($this->afiliado_id);
        }
        return null;
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(PssContract::class, 'pss_contract_id');
    }

    public function tariffItem(): BelongsTo
    {
        return $this->belongsTo(PssTariffItem::class, 'pss_tariff_item_id');
    }
}
