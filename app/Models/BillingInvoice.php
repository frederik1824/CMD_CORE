<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingInvoice extends Model
{
    protected $table = 'billing_invoices';
    protected $fillable = ['invoice_number', 'health_plan_id', 'affiliate_group_id', 'amount', 'ncf', 'status', 'issued_at', 'due_date'];

    public function plan()
    {
        return $this->belongsTo(HealthPlan::class, "health_plan_id");
    }

    public function group()
    {
        return $this->belongsTo(AffiliateGroup::class, "affiliate_group_id");
    }

}
