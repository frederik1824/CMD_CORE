<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthPlanCoverage extends Model
{
    protected $table = 'health_plan_coverages';
    protected $fillable = ['health_plan_id', 'pdss_service_id', 'service_code', 'coverage_percent', 'copay_percent', 'fixed_copay', 'limit_amount', 'limit_period', 'waiting_period_days', 'requires_authorization', 'requires_audit', 'status'];

    public function plan()
    {
        return $this->belongsTo(HealthPlan::class, "health_plan_id");
    }

    public function service()
    {
        return $this->belongsTo(PdssService::class, "pdss_service_id");
    }

}
