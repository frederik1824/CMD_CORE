<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispersionCutDetail extends Model
{
    protected $table = 'dispersion_cut_details';

    protected $fillable = [
        'dispersion_cut_id',
        'capitation_notification_id',
        'afiliado_id',
        'amount',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function cut(): BelongsTo
    {
        return $this->belongsTo(DispersionCut::class, 'dispersion_cut_id');
    }

    public function capitation(): BelongsTo
    {
        return $this->belongsTo(CapitationNotification::class, 'capitation_notification_id');
    }

    public function afiliado(): BelongsTo
    {
        return $this->belongsTo(Afiliado::class, 'afiliado_id');
    }
}
