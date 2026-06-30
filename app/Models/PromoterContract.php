<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoterContract extends Model
{
    protected $table = 'promoter_contracts';
    protected $fillable = ['promoter_id', 'contract_number', 'start_date', 'end_date', 'commission_percent', 'status'];

    public function promoter()
    {
        return $this->belongsTo(Promoter::class, "promoter_id");
    }

}
