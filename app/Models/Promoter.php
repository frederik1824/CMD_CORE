<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promoter extends Model
{
    protected $table = 'promoters';
    protected $fillable = ['name', 'promoter_type', 'identification_number', 'phone', 'email', 'address', 'status'];

    public function contracts()
    {
        return $this->hasMany(PromoterContract::class, "promoter_id");
    }

    public function commissions()
    {
        return $this->hasMany(PromoterCommission::class, "promoter_id");
    }

}
