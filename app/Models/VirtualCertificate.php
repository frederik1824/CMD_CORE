<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VirtualCertificate extends Model
{
    protected $table = 'virtual_certificates';

    protected $fillable = [
        'user_id',
        'course_id',
        'certificate_code',
        'issued_at'
    ];

    protected $casts = [
        'issued_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(VirtualCourse::class, 'course_id');
    }
}
