<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PdssPlan extends Model
{
    protected $table = 'pdss_plans';

    protected $fillable = [
        'plan_number',
        'name',
        'resolution',
        'version',
        'source_file',
        'imported_at',
        'is_active'
    ];

    protected $casts = [
        'imported_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function groups(): HasMany
    {
        return $this->hasMany(PdssGroup::class, 'pdss_plan_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(PdssService::class, 'pdss_plan_id');
    }
}
