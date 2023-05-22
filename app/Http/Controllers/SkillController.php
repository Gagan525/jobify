<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    //
    public function getAllSkills()
    {
        try {
            // Fetch all records from the skills table
            $skills = Skill::all(['id', 'skill', 'skill_slug']);

            return response()->json(['status' => 'success', 'status' => 'success', 'skills' => $skills], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'failed', 'status' => 'failed', 'error' => $e], 500);
        }
    }
}
