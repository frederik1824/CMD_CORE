<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthorizationOverride extends Model
{
    protected $table = 'authorization_overrides';

    protected $fillable = [
        'authorization_id',
        'override_type',
        'original_result',
        'new_result',
        'reason',
        'approved_by',
        'approved_at',
        'requires_supervisor_approval',
        'supervisor_id',
        'supervisor_approved_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'requires_supervisor_approval' => 'boolean',
        'supervisor_approved_at' => 'datetime'
    ];

    public function authorization(): BelongsTo
    {
        return $this->belongsTo(Autorizacion::class, 'authorization_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
}
