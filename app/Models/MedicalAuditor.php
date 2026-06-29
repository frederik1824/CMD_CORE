<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalAuditor extends Model
{
    protected $table = 'medical_auditors';

    protected $fillable = [
        'user_id',
        'auditor_code',
        'exequatur',
        'auditor_type',
        'professional_type',
        'status',
        'registered_at',
        'expires_at',
        'observations',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
