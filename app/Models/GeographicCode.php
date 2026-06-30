<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeographicCode extends Model
{
    protected $table = 'geographic_codes';
    protected $fillable = ['region', 'province', 'municipality', 'sector', 'status'];

}
