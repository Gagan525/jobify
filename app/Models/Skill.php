<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    public function jobs()
    {
        return $this->belongsToMany(Job::class, 'job_skill_rel');
    }

    public function candidates()
    {
        return $this->belongsToMany(CandidateInfo::class, 'cand_skill_rel', 'skill_id', 'cand_info_id')
            ->withTimestamps();
    }

}
