<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AuthorizationClaim extends Model
{
    protected $table = 'authorization_claims';

    protected $fillable = [
        'claim_number',
        'authorization_id',
        'pss_id',
        'afiliado_id',
        'invoice_number',
        'ncf',
        'service_date',
        'received_at',
        'claimed_amount',
        'authorized_amount',
        'approved_amount',
        'objected_amount',
        'status',
        'claim_origin_type',
        'submitted_by',
        'received_by',
        'observations',
    ];

    protected $casts = [
        'service_date' => 'date',
        'received_at' => 'datetime',
        'claimed_amount' => 'decimal:2',
        'authorized_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'objected_amount' => 'decimal:2',
    ];

    public function authorization(): BelongsTo
    {
        return $this->belongsTo(Autorizacion::class, 'authorization_id');
    }

    public function pss(): BelongsTo
    {
        return $this->belongsTo(Pss::class, 'pss_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ClaimDocument::class, 'claim_id');
    }

    public function audits(): HasMany
    {
        return $this->hasMany(ClaimAudit::class, 'claim_id');
    }

    public function payables(): HasMany
    {
        return $this->hasMany(AccountPayable::class, 'claim_id');
    }

    public function glosses(): HasMany
    {
        return $this->hasMany(ClaimGlosa::class, 'claim_id');
    }

    public function getAfiliadoAttribute()
    {
        // Obtener afiliado (titular o dependiente) resolviendo a través de la autorización
        return $this->authorization ? $this->authorization->afiliado : null;
    }
}
