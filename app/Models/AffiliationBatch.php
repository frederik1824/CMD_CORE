<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AffiliationBatch extends Model
{
    protected $table = 'affiliation_batches';

    protected $fillable = [
        'batch_number',
        'batch_type',
        'unipago_lote_id',
        'status',
        'total_records',
        'total_ok',
        'total_pending',
        'total_rejected',
        'submitted_by',
        'submitted_at',
        'processed_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(AffiliationBatchDetail::class, 'affiliation_batch_id');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
}
