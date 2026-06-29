<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimConciliation extends Model
{
    protected $table = 'claim_conciliations';

    protected $fillable = [
        'claim_id',
        'glosa_id',
        'instance',
        'requested_by',
        'requested_at',
        'scheduled_at',
        'resolved_at',
        'result_status',
        'agreement_amount',
        'ars_observation',
        'pss_observation',
        'final_decision',
        'signed_document_path',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'resolved_at' => 'datetime',
        'agreement_amount' => 'decimal:2',
    ];

    public function claim(): BelongsTo
    {
        return $this->belongsTo(AuthorizationClaim::class, 'claim_id');
    }

    public function glosa(): BelongsTo
    {
        return $this->belongsTo(ClaimGlosa::class, 'glosa_id');
    }
}
