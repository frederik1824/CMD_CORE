<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegulatoryPeriod extends Model
{
    protected $fillable = [
        'period_code',
        'month',
        'year',
        'start_date',
        'end_date',
        'status'
    ];

    public function runs()
    {
        return $this->hasMany(RegulatorySchemaRun::class);
    }
}
