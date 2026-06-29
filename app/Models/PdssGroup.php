<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PdssGroup extends Model
{
    protected $table = 'pdss_groups';

    protected $fillable = [
        'pdss_plan_id',
        'code',
        'name',
        'description',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(PdssPlan::class, 'pdss_plan_id');
    }

    public function subgroups(): HasMany
    {
        return $this->hasMany(PdssSubgroup::class, 'pdss_group_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(PdssService::class, 'pdss_group_id');
    }
}
