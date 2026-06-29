<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnipagoMockScenario extends Model
{
    protected $table = 'unipago_mock_scenarios';

    protected $fillable = [
        'service_code',
        'scenario_name',
        'conditions',
        'response_code',
        'response_payload_template',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'conditions' => 'json',
        'response_payload_template' => 'json',
    ];
}
