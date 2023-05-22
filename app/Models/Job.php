<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Application;

class Job extends Model
{
    use HasFactory;

    protected $table = "jobs";

    protected $fillable = [
        'title',
        'description',
        'company',
        'requirements',
        'location',
        'min_salary',
        'max_salary',
        'min_exp',
        'max_exp',
        'notice',
        'status',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'job_skill_rel');
    }
}
