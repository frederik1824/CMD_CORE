<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CapitationNotification extends Model
{
    protected $table = 'capitation_notifications';

    protected $fillable = [
        'notification_number',
        'afiliado_id',
        'period',
        'capitation_amount',
        'individualization_type',
        'status',
        'notified_at',
        'confirmed_at',
        'rejected_at',
        'rejection_reason',
    ];

    protected $casts = [
        'capitation_amount' => 'decimal:2',
        'notified_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function afiliado(): BelongsTo
    {
        return $this->belongsTo(Afiliado::class, 'afiliado_id');
    }
}
