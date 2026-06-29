<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PdssCoverageAccumulator extends Model
{
    protected $table = 'pdss_coverage_accumulators';

    protected $fillable = [
        'afiliado_id',
        'pdss_service_id',
        'service_group',
        'period_year',
        'event_key',
        'accumulated_authorized_amount',
        'accumulated_claimed_amount',
        'accumulated_paid_amount',
        'available_amount',
    ];

    protected $casts = [
        'accumulated_authorized_amount' => 'decimal:2',
        'accumulated_claimed_amount' => 'decimal:2',
        'accumulated_paid_amount' => 'decimal:2',
        'available_amount' => 'decimal:2',
    ];

    public function afiliado(): BelongsTo
    {
        return $this->belongsTo(Afiliado::class, 'afiliado_id');
    }

    public function pdssService(): BelongsTo
    {
        return $this->belongsTo(PdssService::class, 'pdss_service_id');
    }
}
