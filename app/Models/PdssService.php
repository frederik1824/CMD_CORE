<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PdssService extends Model
{
    protected $table = 'pdss_services';

    protected $fillable = [
        'pdss_plan_id',
        'pdss_group_id',
        'pdss_subgroup_id',
        'simon_code',
        'coverage_type',
        'coverage_description',
        'cups_code',
        'level_1_covered',
        'level_2_covered',
        'level_3_covered',
        'amount_coverage',
        'copay_type',
        'requires_authorization',
        'requires_medical_audit',
        'is_high_cost',
        'is_emergency',
        'is_hospitalization',
        'is_surgery',
        'is_diagnostic_support',
        'is_medicine',
        'is_active',
        'source_page',
        'raw_text'
    ];

    protected $casts = [
        'requires_authorization' => 'boolean',
        'requires_medical_audit' => 'boolean',
        'is_high_cost' => 'boolean',
        'is_emergency' => 'boolean',
        'is_hospitalization' => 'boolean',
        'is_surgery' => 'boolean',
        'is_diagnostic_support' => 'boolean',
        'is_medicine' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(PdssPlan::class, 'pdss_plan_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(PdssGroup::class, 'pdss_group_id');
    }

    public function subgroup(): BelongsTo
    {
        return $this->belongsTo(PdssSubgroup::class, 'pdss_subgroup_id');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(PssServiceContract::class, 'pdss_service_id');
    }
}
