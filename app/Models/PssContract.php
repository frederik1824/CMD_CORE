<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PssContract extends Model
{
    protected $table = 'pss_contracts';

    protected $fillable = [
        'pss_id',
        'contract_number',
        'contract_name',
        'contract_type',
        'start_date',
        'end_date',
        'auto_renewal',
        'status',
        'signed_at',
        'signed_by',
        'document_path',
        'observations'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'auto_renewal' => 'boolean',
        'signed_at' => 'datetime'
    ];

    public function pss(): BelongsTo
    {
        return $this->belongsTo(Pss::class, 'pss_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(PssContractVersion::class, 'pss_contract_id');
    }

    public function tariffSchedules(): HasMany
    {
        return $this->hasMany(PssTariffSchedule::class, 'pss_contract_id');
    }

    public function activeVersion()
    {
        return $this->versions()->where('status', 'vigente')->first();
    }
}
