<?php

namespace App\Http\Controllers;

use App\Models\CandidateInfo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CandidateInfoController extends Controller
{
    //
    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
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
            DB::beginTransaction();
            $candidate = $request->user(); // Get the authenticated user

            // Check if candidate info already exists
            if ($candidate->candidates) {
                $candidateInfo = $candidate->candidates;
            } else {
                // Create new candidate info if it doesn't exist
                $candidateInfo = new CandidateInfo;
            }

            // Update the candidate info using the validated data
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

            // Sync the skills with the candidate info
            $candidateInfo->skills()->sync($request->skills);

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Candidate info created/updated successfully', 'candidate_info' => $candidateInfo], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'failed', 'error' => $e], 500);
        }
    }

    public function getCandidateInfo(Request $request)
    {
        try {
            // Get the authenticated user
            $candidate = $request->user();

            // Check if candidate info exists
            if (!$candidate->candidates) {
                return response()->json(['status' => 'failed', 'error' => 'Candidate info not found'], 404);
            }

            // Retrieve the candidate info with skills
            $candidateInfo = $candidate->candidates;

            // Eager load the skills
            $candidateInfo->load('skills');

            return response()->json(['status' => 'success', 'candidate_info' => $candidateInfo], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'error' => 'Failed to retrieve candidate info.', 'message' => $e->getMessage()], 500);
        }
    }
}
