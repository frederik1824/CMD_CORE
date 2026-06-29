<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReimbursementCase extends Model
{
    protected $table = 'reimbursement_cases';

    protected $fillable = [
        'case_number',
        'afiliado_id',
        'origin',
        'request_channel',
        'request_type',
        'pss_id',
        'service_date',
        'payment_date',
        'requested_amount',
        'approved_amount',
        'rejected_amount',
        'status',
        'received_at',
        'completed_documents_at',
        'response_due_date',
        'responded_at',
        'written_response_path',
        'final_decision',
        'pss_debit_required',
        'related_authorization_id',
        'related_claim_id',
        'created_by',
    ];

    protected $casts = [
        'service_date' => 'date',
        'payment_date' => 'date',
        'requested_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'rejected_amount' => 'decimal:2',
        'received_at' => 'datetime',
        'completed_documents_at' => 'datetime',
        'response_due_date' => 'date',
        'responded_at' => 'datetime',
        'pss_debit_required' => 'boolean',
    ];

    public function afiliado(): BelongsTo
    {
        return $this->belongsTo(Afiliado::class, 'afiliado_id');
    }

    public function pss(): BelongsTo
    {
        return $this->belongsTo(Pss::class, 'pss_id');
    }

    public function authorization(): BelongsTo
    {
        return $this->belongsTo(Autorizacion::class, 'related_authorization_id');
    }

    public function claim(): BelongsTo
    {
        return $this->belongsTo(AuthorizationClaim::class, 'related_claim_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ReimbursementDocument::class, 'reimbursement_case_id');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(ReimbursementAction::class, 'reimbursement_case_id');
    }
}
