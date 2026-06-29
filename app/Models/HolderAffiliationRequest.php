<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HolderAffiliationRequest extends Model
{
    protected $table = 'holder_affiliation_requests';

    protected $fillable = [
        'request_number',
        'affiliate_id',
        'contract_number_id',
        'contract_number',
        'promoter_id',
        'employer_name',
        'employer_rnc',
        'payroll_status',
        'salary_amount',
        'regime_type',
        'channel',
        'status',
        'unipago_batch_id',
        'unipago_request_id',
        'unipago_response_code',
        'unipago_response_message',
        'sent_at',
        'processed_at',
        'created_by',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'processed_at' => 'datetime',
        'salary_amount' => 'decimal:2',
    ];

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Afiliado::class, 'affiliate_id');
    }

    public function contractNumber(): BelongsTo
    {
        return $this->belongsTo(AffiliationContractNumber::class, 'contract_number_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(AffiliateDocument::class, 'request_id')
            ->where('request_type', 'titular');
    }
}
