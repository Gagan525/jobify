<?php

namespace App\Http\Controllers;

use App\Models\CandidateInfo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CandidateInfoController extends Controller
{
    //
    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            // 'candidate_id' => 'required|exists:users,id,role,candidate',
            'current_employer' => 'required',
            'location' => 'required',
            'designation' => 'required',
            'total_experience' => 'required|integer',
            'notice_period' => 'required|integer',
            'current_salary' => 'required|numeric',
            'exp_salary' => 'required|numeric',
            'skills' => 'required|array',
            'skills.*' => 'exists:skills,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'error' => $validator->errors()], 400);
        }

        try {
            // Create the candidate info using the validated data
            $candidateInfo = new CandidateInfo;
            // $candidateInfo->candidate_id = $request->candidate_id;
            $candidateInfo->current_employer = $request->current_employer;
            $candidateInfo->location = $request->location;
            $candidateInfo->designation = $request->designation;
            $candidateInfo->total_experience = $request->total_experience;
            $candidateInfo->notice_period = $request->notice_period;
            $candidateInfo->current_salary = $request->current_salary;
            $candidateInfo->exp_salary = $request->exp_salary;

            // Associate the info with the authenticated user
            $candidate = $request->user(); // Get the authenticated user
            $candidate->candidates()->save($candidateInfo);

            // Save the candidate info
            // $candidateInfo->save();

            // Sync the skills with the candidate info
            $candidateInfo->skills()->sync($request->skills);

            return response()->json(['status' => 'success', 'message' => 'Candidate info created successfully', 'candidate_info' => $candidateInfo], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'failed', 'error' => $e], 500);
        }
    }
}
