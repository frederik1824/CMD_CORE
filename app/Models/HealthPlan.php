<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthPlan extends Model
{
    protected $table = 'health_plans';
    protected $fillable = ['code', 'name', 'plan_type', 'description', 'status', 'effective_from', 'effective_to'];

    public function coverages()
    {
        return $this->hasMany(HealthPlanCoverage::class, "health_plan_id");
    }

    public function limits()
    {
        return $this->hasMany(CoverageLimit::class, "health_plan_id");
    }

    public function derivationRules()
    {
        return $this->hasMany(CoverageDerivationRule::class, "health_plan_id");
    }

}
