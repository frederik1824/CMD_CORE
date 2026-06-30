<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PypRiskGroup extends Model
{
    protected $table = 'pyp_risk_groups';
    protected $fillable = ['name', 'description', 'criteria', 'status'];

}
