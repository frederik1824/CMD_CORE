<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReimbursementAction extends Model
{
    protected $table = 'reimbursement_actions';

    public $timestamps = false;

    protected $fillable = [
        'reimbursement_case_id',
        'action_type',
        'description',
        'user_id',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function reimbursementCase(): BelongsTo
    {
        return $this->belongsTo(ReimbursementCase::class, 'reimbursement_case_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
