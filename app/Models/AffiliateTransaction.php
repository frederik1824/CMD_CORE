<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateTransaction extends Model
{
    protected $table = 'affiliate_transactions';
    protected $fillable = ['affiliate_id', 'affiliate_type', 'transaction_type', 'concept', 'payload_before', 'payload_after', 'user_id'];

    protected $casts = [
        'payload_before' => 'array',
        'payload_after' => 'array',
    ];

    public function affiliate()
    {
        return $this->belongsTo(Afiliado::class, "affiliate_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }

}
