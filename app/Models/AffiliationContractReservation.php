<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliationContractReservation extends Model
{
    protected $table = 'affiliation_contract_reservations';

    protected $fillable = [
        'contract_number_id',
        'reserved_by',
        'reservation_type',
        'expires_at',
        'status',
        'consumed_at',
        'released_at',
        'metadata'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'consumed_at' => 'datetime',
        'released_at' => 'datetime',
        'metadata' => 'json'
    ];

    public function number(): BelongsTo
    {
        return $this->belongsTo(AffiliationContractNumber::class, 'contract_number_id');
    }
}
