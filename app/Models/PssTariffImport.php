<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PssTariffImport extends Model
{
    protected $table = 'pss_tariff_imports';

    protected $fillable = [
        'pss_id',
        'pss_contract_id',
        'file_path',
        'total_rows',
        'imported_rows',
        'rejected_rows',
        'status',
        'errors',
        'imported_by',
        'imported_at'
    ];

    protected $casts = [
        'imported_at' => 'datetime',
        'errors' => 'array'
    ];

    public function pss(): BelongsTo
    {
        return $this->belongsTo(Pss::class, 'pss_id');
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(PssContract::class, 'pss_contract_id');
    }
}
