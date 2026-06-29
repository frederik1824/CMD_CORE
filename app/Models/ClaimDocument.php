<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimDocument extends Model
{
    protected $table = 'claim_documents';

    protected $fillable = [
        'claim_id',
        'document_type',
        'file_path',
        'uploaded_by',
        'uploaded_at',
        'status',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function claim(): BelongsTo
    {
        return $this->belongsTo(AuthorizationClaim::class, 'claim_id');
    }
}
