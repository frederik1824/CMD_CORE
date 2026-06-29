<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LabOrderItem extends Model
{
    protected $table = 'lab_order_items';

    protected $fillable = [
        'lab_order_id',
        'pdss_service_id',
        'simon_code_snapshot',
        'cups_code_snapshot',
        'test_name',
        'coverage_type',
        'contracted_amount',
        'requested_amount',
        'authorized_amount',
        'requires_authorization',
        'requires_audit',
        'status',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(LabOrder::class, 'lab_order_id');
    }

    public function pdssService(): BelongsTo
    {
        return $this->belongsTo(PdssService::class, 'pdss_service_id');
    }

    public function result(): HasOne
    {
        return $this->hasOne(LabResult::class, 'lab_order_item_id');
    }
}
