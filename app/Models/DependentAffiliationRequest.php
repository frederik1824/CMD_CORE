<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DependentAffiliationRequest extends Model
{
    protected $table = 'dependent_affiliation_requests';

    protected $fillable = [
        'request_number',
        'holder_affiliate_id',
        'dependent_affiliate_id',
        'relationship',
        'document_type',
        'document_number',
        'status',
        'unipago_batch_id',
        'unipago_request_id',
        'unipago_response_code',
        'unipago_response_message',
        'sent_at',
        'processed_at',
        'created_by',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function holder(): BelongsTo
    {
        return $this->belongsTo(Afiliado::class, 'holder_affiliate_id');
    }

    public function dependent(): BelongsTo
    {
        return $this->belongsTo(Dependiente::class, 'dependent_affiliate_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(AffiliateDocument::class, 'request_id')
            ->where('request_type', 'dependiente');
    }
}
