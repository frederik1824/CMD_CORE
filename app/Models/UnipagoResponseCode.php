<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnipagoResponseCode extends Model
{
    protected $table = 'unipago_response_codes';

    protected $fillable = [
        'code',
        'type',
        'title',
        'description',
        'recommended_action',
        'is_active',
    ];
}
