<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PharmacyDispensation extends Model
{
    protected $table = 'pharmacy_dispensations';

    protected $fillable = [
        'prescription_id',
        'pss_id',
        'afiliado_id',
        'authorization_id',
        'dispensation_number',
        'dispensed_at',
        'total_amount',
        'ars_amount',
        'affiliate_copay_amount',
        'non_covered_amount',
        'status',
        'created_by',
    ];

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(PharmacyPrescription::class, 'prescription_id');
    }

    public function pss(): BelongsTo
    {
        return $this->belongsTo(Pss::class, 'pss_id');
    }

    public function afiliado(): BelongsTo
    {
        return $this->belongsTo(Afiliado::class, 'afiliado_id');
    }

    public function autorizacion(): BelongsTo
    {
        return $this->belongsTo(Autorizacion::class, 'authorization_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PharmacyDispensationItem::class, 'dispensation_id');
    }
}
