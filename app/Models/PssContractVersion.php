<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PssContractVersion extends Model
{
    protected $table = 'pss_contract_versions';

    protected $fillable = [
        'pss_contract_id',
        'version_number',
        'effective_from',
        'effective_to',
        'status',
        'approved_by',
        'approved_at',
        'change_reason'
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'approved_at' => 'datetime'
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(PssContract::class, 'pss_contract_id');
    }

    public function tariffSchedules(): HasMany
    {
        return $this->hasMany(PssTariffSchedule::class, 'pss_contract_version_id');
    }
}
