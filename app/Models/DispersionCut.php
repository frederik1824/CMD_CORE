<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DispersionCut extends Model
{
    protected $table = 'dispersion_cuts';

    protected $fillable = [
        'cut_number',
        'period',
        'cut_type',
        'status',
        'total_affiliates',
        'total_holders',
        'total_dependents',
        'total_capitations',
        'total_amount',
        'generated_at',
        'certified_at',
        'dispersed_at',
        'closed_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'generated_at' => 'datetime',
        'certified_at' => 'datetime',
        'dispersed_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(DispersionCutDetail::class, 'dispersion_cut_id');
    }
}
