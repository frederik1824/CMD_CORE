<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AuthorizationEngineRule extends Model
{
    protected $table = 'authorization_engine_rules';

    protected $fillable = [
        'rule_code',
        'name',
        'description',
        'process',
        'service_group',
        'service_subgroup',
        'pss_type',
        'pss_id',
        'health_plan_id',
        'origin',
        'condition_json',
        'action_json',
        'priority',
        'severity',
        'status',
        'effective_from',
        'effective_to',
        'created_by'
    ];

    protected $casts = [
        'condition_json' => 'array',
        'action_json' => 'array',
        'priority' => 'integer',
        'effective_from' => 'date',
        'effective_to' => 'date'
    ];

    public function tests(): HasMany
    {
        return $this->hasMany(AuthorizationEngineRuleTest::class, 'rule_id');
    }

    public function executionLogs(): HasMany
    {
        return $this->hasMany(AuthorizationEngineRuleLog::class, 'rule_id');
    }
}
