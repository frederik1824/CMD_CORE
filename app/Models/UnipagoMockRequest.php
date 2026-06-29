<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnipagoMockRequest extends Model
{
    protected $table = 'unipago_mock_requests';

    protected $fillable = [
        'service_code',
        'service_name',
        'endpoint_mock',
        'request_payload',
        'response_payload',
        'status',
        'created_by',
        'processed_at',
    ];

    protected $casts = [
        'request_payload' => 'json',
        'response_payload' => 'json',
        'processed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
