<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateContract extends Model
{
    protected $table = 'affiliate_contracts';
    protected $fillable = ['code', 'name', 'contract_type', 'description', 'status'];

}
