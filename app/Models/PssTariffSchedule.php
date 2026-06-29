<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PssTariffSchedule extends Model
{
    protected $table = 'pss_tariff_schedules';

    protected $fillable = [
        'pss_contract_id',
        'pss_contract_version_id',
        'name',
        'effective_from',
        'effective_to',
        'status',
        'imported_from_file',
        'imported_by',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'imported_from_file' => 'boolean',
        'approved_at' => 'datetime'
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(PssContract::class, 'pss_contract_id');
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(PssContractVersion::class, 'pss_contract_version_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PssTariffItem::class, 'pss_tariff_schedule_id');
    }
}
