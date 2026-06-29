<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthorizationTimelineEvent extends Model
{
    protected $table = 'authorization_timeline_events';

    protected $fillable = [
        'authorization_id',
        'event_type',
        'title',
        'description',
        'old_status',
        'new_status',
        'user_id',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'json',
    ];

    public function authorization(): BelongsTo
    {
        return $this->belongsTo(Autorizacion::class, 'authorization_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Helper para registrar eventos de timeline rápidamente.
     */
    public static function registrar($autId, $type, $title, $description = null, $oldStatus = null, $newStatus = null, $meta = null)
    {
        return self::create([
            'authorization_id' => $autId,
            'event_type' => $type,
            'title' => $title,
            'description' => $description,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'user_id' => auth()->id(),
            'metadata' => $meta,
        ]);
    }
}
