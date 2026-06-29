<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateDocument extends Model
{
    protected $table = 'affiliate_documents';

    protected $fillable = [
        'affiliate_id',
        'request_id',
        'request_type',
        'document_type',
        'file_path',
        'status',
        'uploaded_by',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
