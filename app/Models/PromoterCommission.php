<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoterCommission extends Model
{
    protected $table = 'promoter_commissions';
    protected $fillable = ['promoter_id', 'campaign_id', 'affiliate_id', 'amount', 'payout_period', 'status', 'payment_date'];

    public function promoter()
    {
        return $this->belongsTo(Promoter::class, "promoter_id");
    }

    public function campaign()
    {
        return $this->belongsTo(PromoterCampaign::class, "campaign_id");
    }

    public function affiliate()
    {
        return $this->belongsTo(Afiliado::class, "affiliate_id");
    }

}
