<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PypProgramEnrollment extends Model
{
    protected $table = 'pyp_program_enrollments';
    protected $fillable = ['affiliate_id', 'program_id', 'enrollment_date', 'status', 'cancellation_reason', 'cancelled_at'];

    public function program()
    {
        return $this->belongsTo(PypProgram::class, "program_id");
    }

    public function affiliate()
    {
        return $this->belongsTo(Afiliado::class, "affiliate_id");
    }

}
