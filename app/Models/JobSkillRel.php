<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobSkillRel extends Model
{
    use HasFactory;

    protected $table = 'job_skill_rel';
    protected $primary_key = 'id';
    public $timestamps = true;   
}