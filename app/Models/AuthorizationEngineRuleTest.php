<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthorizationEngineRuleTest extends Model
{
    protected $table = 'authorization_engine_rule_tests';

    protected $fillable = [
        'rule_id',
        'test_payload',
        'result_payload',
        'executed_by',
        'executed_at'
    ];

    protected $casts = [
        'test_payload' => 'array',
        'result_payload' => 'array',
        'executed_at' => 'datetime'
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(AuthorizationEngineRule::class, 'rule_id');
    }
}
