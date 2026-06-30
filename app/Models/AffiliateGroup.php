<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateGroup extends Model
{
    protected $table = 'affiliate_groups';
    protected $fillable = ['name', 'rnc', 'description', 'status'];

}
