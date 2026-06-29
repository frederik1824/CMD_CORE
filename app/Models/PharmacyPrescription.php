<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PharmacyPrescription extends Model
{
    protected $table = 'pharmacy_prescriptions';

    protected $fillable = [
        'pss_id',
        'afiliado_id',
        'prescription_number',
        'doctor_name',
        'doctor_exequatur',
        'specialty',
        'diagnosis',
        'prescription_date',
        'document_path',
        'status',
        'created_by',
    ];

    public function pss(): BelongsTo
    {
        return $this->belongsTo(Pss::class, 'pss_id');
    }

    public function afiliado(): BelongsTo
    {
        return $this->belongsTo(Afiliado::class, 'afiliado_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function dispensations(): HasMany
    {
        return $this->hasMany(PharmacyDispensation::class, 'prescription_id');
    }
}
