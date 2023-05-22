<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Skill;

class SkillsTableSeeder extends Seeder
{
    public function run()
    {
        $skills = [
            'HTML' => 'html',
            'CSS' => 'css',
            'PHP' => 'php',
            'Python' => 'python',
            'Java' => 'java',
            'C#' => 'cSharp',
            'C++' => 'cpp',
            'Ruby' => 'ruby',
            'Swift' => 'swift',
            'React' => 'react',
            'Angular' => 'angular',
            'Vue.js' => 'vue',
            'Node.js' => 'nodejs',
            'Laravel' => 'laravel',
            'Symfony' => 'symphony',
            'ASP.NET' => 'aspdotnet',
            'Django' => 'django',
            'Flask' => 'flask',
            'WordPress' => 'wordpress',
        ];

        foreach ($skills as $skill => $skill_slug) {
            Skill::create([
                'skill' => $skill,
                'skill_slug' => Str::slug($skill_slug)
            ]);
        }
    }
}
