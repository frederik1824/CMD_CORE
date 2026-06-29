<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliationBatchDetail extends Model
{
    protected $table = 'affiliation_batch_details';

    protected $fillable = [
        'affiliation_batch_id',
        'afiliado_id',
        'dependiente_id',
        'request_number',
        'contract_number',
        'status',
        'reason_code',
        'reason_description',
        'raw_response',
    ];

    protected $casts = [
        'raw_response' => 'json',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(AffiliationBatch::class, 'affiliation_batch_id');
    }

    public function afiliado(): BelongsTo
    {
        return $this->belongsTo(Afiliado::class, 'afiliado_id');
    }

    public function dependiente(): BelongsTo
    {
        return $this->belongsTo(Dependiente::class, 'dependiente_id');
    }
}
