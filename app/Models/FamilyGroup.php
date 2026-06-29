<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FamilyGroup extends Model
{
    protected $table = 'family_groups';

    protected $fillable = [
        'holder_affiliate_id',
        'status',
    ];

    public function holder(): BelongsTo
    {
        return $this->belongsTo(Afiliado::class, 'holder_affiliate_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(FamilyGroupMember::class, 'family_group_id');
    }
}
