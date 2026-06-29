<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AffiliationContractRange extends Model
{
    protected $table = 'affiliation_contract_ranges';

    protected $fillable = [
        'range_code',
        'description',
        'start_number',
        'end_number',
        'total_numbers',
        'available_count',
        'reserved_count',
        'used_count',
        'ok_count',
        'pending_count',
        'rejected_count',
        'blocked_count',
        'source',
        'approval_reference',
        'approved_by',
        'approved_at',
        'valid_from',
        'valid_until',
        'status',
        'observations',
        'created_by'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'valid_from' => 'date',
        'valid_until' => 'date',
    ];

    public function numbers(): HasMany
    {
        return $this->hasMany(AffiliationContractNumber::class, 'affiliation_contract_range_id');
    }

    /**
     * Recalcula y actualiza los contadores del rango de forma atómica.
     */
    public function recalculateCounts(): void
    {
        $stats = $this->numbers()
            ->selectRaw("
                COUNT(CASE WHEN status = 'disponible' THEN 1 END) as available,
                COUNT(CASE WHEN status = 'reservado' THEN 1 END) as reserved,
                COUNT(CASE WHEN status = 'usado' THEN 1 END) as used,
                COUNT(CASE WHEN status = 'ok' THEN 1 END) as ok,
                COUNT(CASE WHEN status = 'pe' THEN 1 END) as pending,
                COUNT(CASE WHEN status = 're' THEN 1 END) as rejected,
                COUNT(CASE WHEN status = 'bloqueado' THEN 1 END) as blocked
            ")
            ->first();

        $this->update([
            'available_count' => $stats->available ?? 0,
            'reserved_count' => $stats->reserved ?? 0,
            'used_count' => $stats->used ?? 0,
            'ok_count' => $stats->ok ?? 0,
            'pending_count' => $stats->pending ?? 0,
            'rejected_count' => $stats->rejected ?? 0,
            'blocked_count' => $stats->blocked ?? 0,
            'status' => ($stats->available ?? 0) === 0 ? 'agotado' : 'activo'
        ]);
    }
}
