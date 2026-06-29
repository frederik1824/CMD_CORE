<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PssUser extends Model
{
    protected $table = 'pss_users';

    protected $fillable = [
        'user_id',
        'pss_id',
        'role',
        'access_type',
        'is_default',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pss(): BelongsTo
    {
        return $this->belongsTo(Pss::class, 'pss_id');
    }
}
