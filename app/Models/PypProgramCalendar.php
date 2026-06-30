<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PypProgramCalendar extends Model
{
    protected $table = 'pyp_program_calendar';
    protected $fillable = ['program_id', 'service_name', 'scheduled_date', 'location', 'capacity', 'status'];

    public function program()
    {
        return $this->belongsTo(PypProgram::class, "program_id");
    }

}
