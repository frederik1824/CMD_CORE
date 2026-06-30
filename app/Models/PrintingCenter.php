<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrintingCenter extends Model
{
    protected $table = 'printing_centers';
    protected $fillable = ['name', 'location', 'contact_person', 'status'];

}
