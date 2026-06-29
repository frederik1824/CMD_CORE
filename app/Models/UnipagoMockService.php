<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnipagoMockService extends Model
{
    protected $table = 'unipago_mock_services';

    protected $fillable = [
        'service_code',
        'service_name',
        'description',
        'endpoint_mock',
        'method',
        'protocol',
        'is_active',
        'default_response_type',
        'simulated_latency_ms',
        'error_probability',
    ];
}
