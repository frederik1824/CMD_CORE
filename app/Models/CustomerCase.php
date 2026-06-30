<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerCase extends Model
{
    protected $table = 'customer_cases';
    protected $fillable = ['affiliate_id', 'case_type', 'description', 'status', 'priority', 'sla_hours', 'resolved_at', 'resolution_details'];

    public function affiliate()
    {
        return $this->belongsTo(Afiliado::class, "affiliate_id");
    }

}
