<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyGroupMember extends Model
{
    protected $table = 'family_group_members';

    protected $fillable = [
        'family_group_id',
        'affiliate_id',
        'relationship',
        'status',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(FamilyGroup::class, 'family_group_id');
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Dependiente::class, 'affiliate_id');
    }
}
