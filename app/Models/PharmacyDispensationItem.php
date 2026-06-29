<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PharmacyDispensationItem extends Model
{
    protected $table = 'pharmacy_dispensation_items';

    protected $fillable = [
        'dispensation_id',
        'pdss_service_id',
        'medicine_code',
        'medicine_name',
        'presentation',
        'concentration',
        'quantity',
        'unit_price',
        'total_price',
        'ars_covered_amount',
        'copay_amount',
        'non_covered_amount',
        'requires_authorization',
        'status',
    ];

    public function dispensation(): BelongsTo
    {
        return $this->belongsTo(PharmacyDispensation::class, 'dispensation_id');
    }

    public function pdssService(): BelongsTo
    {
        return $this->belongsTo(PdssService::class, 'pdss_service_id');
    }
}
