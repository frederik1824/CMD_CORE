<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliationContractMovement extends Model
{
    protected $table = 'affiliation_contract_movements';
    public $timestamps = false;

    protected $fillable = [
        'contract_number_id',
        'movement_type',
        'old_status',
        'new_status',
        'user_id',
        'affiliate_id',
        'batch_id',
        'lote_id',
        'description',
        'metadata',
        'created_at'
    ];

    protected $casts = [
        'metadata' => 'json',
        'created_at' => 'datetime'
    ];

    public function number(): BelongsTo
    {
        return $this->belongsTo(AffiliationContractNumber::class, 'contract_number_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Afiliado::class, 'affiliate_id');
    }
}
