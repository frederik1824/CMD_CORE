<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderNetworkPlan extends Model
{
    protected $table = 'provider_network_plan';
    protected $fillable = ['provider_network_id', 'health_plan_id'];

}
