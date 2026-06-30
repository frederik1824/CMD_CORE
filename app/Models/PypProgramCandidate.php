<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PypProgramCandidate extends Model
{
    protected $table = 'pyp_program_candidates';
    protected $fillable = ['affiliate_id', 'program_id', 'risk_group_id', 'source', 'status', 'reason_not_enrolled', 'detected_at'];

    public function program()
    {
        return $this->belongsTo(PypProgram::class, "program_id");
    }

    public function riskGroup()
    {
        return $this->belongsTo(PypRiskGroup::class, "risk_group_id");
    }

    public function affiliate()
    {
        return $this->belongsTo(Afiliado::class, "affiliate_id");
    }

}
