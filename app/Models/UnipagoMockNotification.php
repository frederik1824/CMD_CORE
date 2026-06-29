<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnipagoMockNotification extends Model
{
    protected $table = 'unipago_mock_notifications';

    protected $fillable = [
        'notification_type',
        'reference_type',
        'reference_id',
        'title',
        'message',
        'read_at',
        'metadata',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'metadata' => 'json',
    ];

    /**
     * Helper para registrar una notificación rápidamente.
     */
    public static function enviar($type, $refType, $refId, $title, $message, $meta = null)
    {
        return self::create([
            'notification_type' => $type,
            'reference_type' => $refType,
            'reference_id' => $refId,
            'title' => $title,
            'message' => $message,
            'metadata' => $meta,
        ]);
    }
}
