<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClaimGlosa extends Model
{
    protected $table = 'claim_glosses';

    protected $fillable = [
        'claim_id',
        'claim_detail_id',
        'audit_id',
        'glosa_code',
        'glosa_type',
        'objected_service',
        'objection_reason',
        'evidence_reference',
        'original_amount',
        'objected_amount',
        'recognized_amount',
        'status',
        'created_by',
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'objected_amount' => 'decimal:2',
        'recognized_amount' => 'decimal:2',
    ];

    public function claim(): BelongsTo
    {
        return $this->belongsTo(AuthorizationClaim::class, 'claim_id');
    }

    public function audit(): BelongsTo
    {
        return $this->belongsTo(ClaimAudit::class, 'audit_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function conciliations(): HasMany
    {
        return $this->hasMany(ClaimConciliation::class, 'glosa_id');
    }
}
