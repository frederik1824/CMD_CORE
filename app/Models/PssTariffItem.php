<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PssTariffItem extends Model
{
    protected $table = 'pss_tariff_items';

    protected $fillable = [
        'pss_tariff_schedule_id',
        'pss_id',
        'pdss_service_id',
        'simon_code_snapshot',
        'cups_code_snapshot',
        'service_description_snapshot',
        'service_group_snapshot',
        'service_subgroup_snapshot',
        'coverage_type_snapshot',
        'contracted_amount',
        'currency',
        'copay_percent',
        'affiliate_copay_amount',
        'ars_covered_percent',
        'requires_authorization',
        'requires_medical_audit',
        'requires_document',
        'frequency_limit',
        'frequency_period',
        'max_amount_per_event',
        'max_amount_per_year',
        'level_1_allowed',
        'level_2_allowed',
        'level_3_allowed',
        'is_high_cost',
        'is_emergency',
        'is_hospitalization',
        'is_surgery',
        'is_diagnostic_support',
        'is_medicine',
        'status'
    ];

    protected $casts = [
        'contracted_amount' => 'decimal:2',
        'copay_percent' => 'decimal:2',
        'affiliate_copay_amount' => 'decimal:2',
        'ars_covered_percent' => 'decimal:2',
        'requires_authorization' => 'boolean',
        'requires_medical_audit' => 'boolean',
        'requires_document' => 'boolean',
        'level_1_allowed' => 'boolean',
        'level_2_allowed' => 'boolean',
        'level_3_allowed' => 'boolean',
        'is_high_cost' => 'boolean',
        'is_emergency' => 'boolean',
        'is_hospitalization' => 'boolean',
        'is_surgery' => 'boolean',
        'is_diagnostic_support' => 'boolean',
        'is_medicine' => 'boolean',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(PssTariffSchedule::class, 'pss_tariff_schedule_id');
    }

    public function pss(): BelongsTo
    {
        return $this->belongsTo(Pss::class, 'pss_id');
    }

    public function pdssService(): BelongsTo
    {
        return $this->belongsTo(PdssService::class, 'pdss_service_id');
    }
}
