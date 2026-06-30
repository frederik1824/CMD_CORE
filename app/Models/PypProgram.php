<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PypProgram extends Model
{
    protected $table = 'pyp_programs';
    protected $fillable = ['name', 'program_type', 'description', 'target_population', 'risk_group_id', 'status', 'start_date', 'end_date', 'created_by'];

    public function riskGroup()
    {
        return $this->belongsTo(PypRiskGroup::class, "risk_group_id");
    }

    public function candidates()
    {
        return $this->hasMany(PypProgramCandidate::class, "program_id");
    }

    public function enrollments()
    {
        return $this->hasMany(PypProgramEnrollment::class, "program_id");
    }

    public function calendar()
    {
        return $this->hasMany(PypProgramCalendar::class, "program_id");
    }

}
