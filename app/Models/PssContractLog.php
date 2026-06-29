<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PssContractLog extends Model
{
    protected $table = 'pss_contract_logs';
    public $timestamps = false; // Solo usa created_at por defecto

    protected $fillable = [
        'pss_contract_id',
        'user_id',
        'action',
        'old_values',
        'new_values',
        'observation'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime'
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(PssContract::class, 'pss_contract_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
