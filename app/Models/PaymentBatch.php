<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentBatch extends Model
{
    protected $table = 'payment_batches';

    protected $fillable = [
        'batch_number',
        'status',
        'total_amount',
        'total_items',
        'scheduled_payment_date',
        'created_by',
        'approved_by',
        'paid_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'scheduled_payment_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PaymentBatchItem::class, 'payment_batch_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function reconciliations(): HasMany
    {
        return $this->hasMany(PaymentReconciliation::class, 'payment_batch_id');
    }
}
