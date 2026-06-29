<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AffiliationContractNumber extends Model
{
    protected $table = 'affiliation_contract_numbers';

    protected $fillable = [
        'affiliation_contract_range_id',
        'contract_number',
        'status',
        'assigned_to_user_id',
        'assigned_to_promoter_id',
        'assigned_to_affiliate_id',
        'assigned_to_batch_id',
        'reservation_token',
        'reserved_at',
        'reservation_expires_at',
        'used_at',
        'sent_to_unipago_at',
        'unipago_lote_id',
        'unipago_request_id',
        'unipago_response_status',
        'unipago_response_code',
        'unipago_response_message',
        'released_at',
        'released_by',
        'release_reason',
        'blocked_at',
        'blocked_by',
        'block_reason',
        'observations'
    ];

    protected $casts = [
        'reserved_at' => 'datetime',
        'reservation_expires_at' => 'datetime',
        'used_at' => 'datetime',
        'sent_to_unipago_at' => 'datetime',
        'released_at' => 'datetime',
        'blocked_at' => 'datetime',
    ];

    public function range(): BelongsTo
    {
        return $this->belongsTo(AffiliationContractRange::class, 'affiliation_contract_range_id');
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Afiliado::class, 'assigned_to_affiliate_id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(AffiliationContractMovement::class, 'contract_number_id');
    }

    public function activeReservation()
    {
        return $this->hasOne(AffiliationContractReservation::class, 'contract_number_id')
            ->where('status', 'activa');
    }
}
