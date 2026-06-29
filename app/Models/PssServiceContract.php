<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PssServiceContract extends Model
{
    protected $table = 'pss_service_contracts';

    protected $fillable = [
        'pss_id',
        'pdss_service_id',
        'contracted_amount',
        'authorization_required',
        'audit_required',
        'frequency_limit',
        'frequency_period',
        'is_active'
    ];

    protected $casts = [
        'authorization_required' => 'boolean',
        'audit_required' => 'boolean',
        'is_active' => 'boolean',
        'contracted_amount' => 'decimal:2'
    ];

    public function pss(): BelongsTo
    {
        return $this->belongsTo(Pss::class, 'pss_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(PdssService::class, 'pdss_service_id');
    }
}
