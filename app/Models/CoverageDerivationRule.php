<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoverageDerivationRule extends Model
{
    protected $table = 'coverage_derivation_rules';
    protected $fillable = ['health_plan_id', 'derivation_type', 'condition_json', 'result_json', 'priority', 'status'];

    protected $casts = [
        'condition_json' => 'array',
        'result_json' => 'array',
    ];

    public function plan()
    {
        return $this->belongsTo(HealthPlan::class, "health_plan_id");
    }

}
