<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalEntryLine extends Model
{
    protected $table = 'journal_entry_lines';

    protected $fillable = [
        'journal_entry_id',
        'account_id',
        'debit',
        'credit',
        'description',
        'third_party_type',
        'third_party_id',
        'cost_center_id',
        'metadata',
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartAccount::class, 'account_id');
    }

    public function thirdParty()
    {
        if ($this->third_party_type === 'pss') {
            return $this->belongsTo(Pss::class, 'third_party_id');
        } elseif ($this->third_party_type === 'afiliado') {
            return $this->belongsTo(Afiliado::class, 'third_party_id');
        } elseif ($this->third_party_type === 'promotor') {
            // Suponemos que Promotor es User o Pss, dependiendo del sistema.
            return $this->belongsTo(User::class, 'third_party_id');
        }
        return null;
    }
}
