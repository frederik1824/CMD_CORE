<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderGroup extends Model
{
    protected $table = 'provider_groups';
    protected $fillable = ['name', 'description', 'status'];

}
