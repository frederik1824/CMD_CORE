<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PdssImportLog extends Model
{
    protected $table = 'pdss_import_logs';

    protected $fillable = [
        'source_file',
        'total_pages',
        'total_groups',
        'total_subgroups',
        'total_services',
        'imported_by',
        'status',
        'errors',
        'started_at',
        'finished_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime'
    ];
}
