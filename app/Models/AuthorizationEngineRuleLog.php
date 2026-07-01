<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthorizationEngineRuleLog extends Model
{
    protected $table = 'authorization_engine_rule_logs';

    protected $fillable = [
        'rule_id',
        'authorization_id',
        'result',
        'message',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(AuthorizationEngineRule::class, 'rule_id');
    }
}
