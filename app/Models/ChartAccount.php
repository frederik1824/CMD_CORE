<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChartAccount extends Model
{
    protected $table = 'chart_accounts';

    protected $fillable = [
        'code',
        'name',
        'account_class',
        'parent_id',
        'level',
        'account_type',
        'nature',
        'is_postable',
        'is_system',
        'allows_children',
        'sisalril_required',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_postable' => 'boolean',
        'is_system' => 'boolean',
        'allows_children' => 'boolean',
        'sisalril_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('code');
    }

    public function entryLines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class, 'account_id');
    }
}
