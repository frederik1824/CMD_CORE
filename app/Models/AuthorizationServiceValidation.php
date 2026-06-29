<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthorizationServiceValidation extends Model
{
    protected $table = 'authorization_service_validations';

    protected $fillable = [
        'authorization_id',
        'pdss_service_id',
        'validation_type',
        'status',
        'message',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(PdssService::class, 'pdss_service_id');
    }
}
