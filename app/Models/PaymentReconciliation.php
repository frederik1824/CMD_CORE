<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentReconciliation  extends Model
{
    protected $table = 'payment_reconciliations';

    protected $fillable = [
        'payment_batch_id',
        'account_payable_id',
        'pss_id',
        'expected_amount',
        'paid_amount',
        'difference',
        'bank_reference',
        'payment_date',
        'status',
        'observations',
    ];

    protected $casts = [
        'expected_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'difference' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(PaymentBatch::class, 'payment_batch_id');
    }

    public function payable(): BelongsTo
    {
        return $this->belongsTo(AccountPayable::class, 'account_payable_id');
    }

    public function pss(): BelongsTo
    {
        return $this->belongsTo(Pss::class, 'pss_id');
    }
}
