<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReimbursementDocument extends Model
{
    protected $table = 'reimbursement_documents';

    protected $fillable = [
        'reimbursement_case_id',
        'document_type',
        'file_path',
        'status',
        'uploaded_at',
        'uploaded_by',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function reimbursementCase(): BelongsTo
    {
        return $this->belongsTo(ReimbursementCase::class, 'reimbursement_case_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
