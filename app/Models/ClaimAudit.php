<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimAudit extends Model
{
    protected $table = 'claim_audits';

    protected $fillable = [
        'claim_id',
        'audit_type',
        'auditor_id',
        'status',
        'claimed_amount',
        'approved_amount',
        'objected_amount',
        'objection_reason',
        'internal_observation',
        'pss_observation',
        'reviewed_at',
    ];

    protected $casts = [
        'claimed_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'objected_amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    public function claim(): BelongsTo
    {
        return $this->belongsTo(AuthorizationClaim::class, 'claim_id');
    }

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }
}
