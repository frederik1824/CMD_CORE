<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LabOrder extends Model
{
    protected $table = 'lab_orders';

    protected $fillable = [
        'pss_id',
        'afiliado_id',
        'order_number',
        'doctor_name',
        'doctor_exequatur',
        'specialty',
        'diagnosis',
        'order_date',
        'document_path',
        'status',
        'created_by',
    ];

    public function pss(): BelongsTo
    {
        return $this->belongsTo(Pss::class, 'pss_id');
    }

    public function afiliado(): BelongsTo
    {
        return $this->belongsTo(Afiliado::class, 'afiliado_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(LabOrderItem::class, 'lab_order_id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(LabResult::class, 'lab_order_id');
    }
}
