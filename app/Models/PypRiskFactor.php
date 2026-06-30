<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PypRiskFactor extends Model
{
    protected $table = 'pyp_risk_factors';
    protected $fillable = ['name', 'description', 'status'];

}
