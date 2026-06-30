<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderGeoLocation extends Model
{
    protected $table = 'provider_geo_locations';
    protected $fillable = ['pss_id', 'province', 'municipality', 'sector', 'latitude', 'longitude', 'address_details'];

    public function pss()
    {
        return $this->belongsTo(Pss::class, "pss_id");
    }

}
