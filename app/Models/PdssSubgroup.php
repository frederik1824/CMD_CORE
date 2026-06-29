<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PdssSubgroup extends Model
{
    protected $table = 'pdss_subgroups';

    protected $fillable = [
        'pdss_group_id',
        'code',
        'name',
        'amount_coverage',
        'copay_type',
        'description',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(PdssGroup::class, 'pdss_group_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(PdssService::class, 'pdss_subgroup_id');
    }
}
