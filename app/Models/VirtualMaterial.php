<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VirtualMaterial extends Model
{
    protected $table = 'virtual_materials';

    protected $fillable = [
        'lesson_id',
        'name',
        'file_path',
        'file_type',
        'size_bytes'
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(VirtualLesson::class, 'lesson_id');
    }
}
