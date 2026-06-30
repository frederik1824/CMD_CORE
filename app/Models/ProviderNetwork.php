<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderNetwork extends Model
{
    protected $table = 'provider_networks';
    protected $fillable = ['name', 'description', 'status'];

    public function plans()
    {
        return $this->belongsToMany(HealthPlan::class, "provider_network_plan", "provider_network_id", "health_plan_id");
    }

}
