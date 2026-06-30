<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoterCampaign extends Model
{
    protected $table = 'promoter_campaigns';
    protected $fillable = ['name', 'description', 'start_date', 'end_date', 'commission_amount', 'status'];

}
