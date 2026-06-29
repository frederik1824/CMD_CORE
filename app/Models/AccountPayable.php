<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AccountPayable extends Model
{
    protected $table = 'accounts_payable';

    protected $fillable = [
        'payable_number',
        'account_payable_number',
        'claim_id',
        'authorization_id',
        'pss_id',
        'amount',
        'retained_amount',
        'gross_amount',
        'objected_amount',
        'approved_amount',
        'tax_withholding_amount',
        'other_deductions',
        'net_amount',
        'vendor_type',
        'vendor_id',
        'status',
        'generated_by',
        'generated_at',
        'due_date',
        'accounting_entry_id',
        'payment_entry_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'retained_amount' => 'decimal:2',
        'gross_amount' => 'decimal:2',
        'objected_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'tax_withholding_amount' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'generated_at' => 'datetime',
        'due_date' => 'date',
    ];

    public function claim(): BelongsTo
    {
        return $this->belongsTo(AuthorizationClaim::class, 'claim_id');
    }

    public function authorization(): BelongsTo
    {
        return $this->belongsTo(Autorizacion::class, 'authorization_id');
    }

    public function pss(): BelongsTo
    {
        return $this->belongsTo(Pss::class, 'pss_id');
    }

    public function batchItem(): HasOne
    {
        return $this->hasOne(PaymentBatchItem::class, 'account_payable_id');
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
