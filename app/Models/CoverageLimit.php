<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoverageLimit extends Model
{
    protected $table = 'coverage_limits';
    protected $fillable = ['health_plan_id', 'service_group', 'origin', 'affiliate_id', 'limit_type', 'amount', 'period', 'status'];

    public function plan()
    {
        return $this->belongsTo(HealthPlan::class, "health_plan_id");
    }

}
