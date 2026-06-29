<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PdssCoverageRule extends Model
{
    protected $table = 'pdss_coverage_rules';

    protected $fillable = [
        'plan_code',
        'plan_name',
        'effective_date',
        'service_group',
        'service_subgroup',
        'coverage_limit_type',
        'coverage_limit_amount',
        'coverage_percent_ars',
        'copay_percent_affiliate',
        'copay_fixed_amount',
        'copay_cap_amount',
        'annual_limit',
        'event_limit',
        'daily_limit',
        'requires_continuity_validation',
        'requires_seniority_validation',
        'requires_authorization',
        'requires_medical_audit',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'coverage_limit_amount' => 'decimal:2',
        'coverage_percent_ars' => 'decimal:2',
        'copay_percent_affiliate' => 'decimal:2',
        'copay_fixed_amount' => 'decimal:2',
        'copay_cap_amount' => 'decimal:2',
        'annual_limit' => 'decimal:2',
        'event_limit' => 'decimal:2',
        'daily_limit' => 'decimal:2',
        'requires_continuity_validation' => 'boolean',
        'requires_seniority_validation' => 'boolean',
        'requires_authorization' => 'boolean',
        'requires_medical_audit' => 'boolean',
        'is_active' => 'boolean',
    ];
}
