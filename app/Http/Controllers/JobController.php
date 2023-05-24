<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class JobController extends Controller
{
    //
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'company' => 'required',
            'location' => 'required',
            'min_salary' => 'required|numeric',
            'max_salary' => 'required|numeric',
            'min_exp' => 'required|numeric',
            'max_exp' => 'required|numeric',
            'notice' => 'required',
            'status' => 'required|in:active,archive',
            'skillIds' => 'required|array', // Validate that skillIds is an array
            'skillIds.*' => 'exists:skills,id', // Validate that each skillId exists in the skills table    
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        try {
            DB::beginTransaction();
            // Create the job using the validated data
            $job = new Job;
            $job->title = $request->title;
            $job->description = $request->description;
            $job->company = $request->company;
            $job->location = $request->location;
            $job->min_salary = $request->min_salary;
            $job->max_salary = $request->max_salary;
            $job->min_exp = $request->min_exp;
            $job->max_exp = $request->max_exp;
            $job->notice = $request->notice;
            $job->status = $request->status;

            // Associate the job with the authenticated user
            $user = $request->user(); // Get the authenticated user
            $user->jobs()->save($job);

            // Attach the skillIds to the job using the job_skill_rel table
            $job->skills()->attach($request->skillIds);

            DB::commit();

            return response()->json(['message' => 'Job created successfully', 'job' => $job], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create job. Please try again.', 'message' => $e], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'company' => 'required',
            'location' => 'required',
            'min_salary' => 'required|numeric',
            'max_salary' => 'required|numeric',
            'min_exp' => 'required|numeric',
            'max_exp' => 'required|numeric',
            'notice' => 'required',
            'status' => 'required|in:active,archive',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'error' => $validator->errors()], 400);
        }

        try {
            // Find the job based on the provided ID
            $job = Job::findOrFail($id);

            // Update the job with the validated data
            $job->title = $request->title;
            $job->description = $request->description;
            $job->company = $request->company;
            $job->location = $request->location;
            $job->min_salary = $request->min_salary;
            $job->max_salary = $request->max_salary;
            $job->min_exp = $request->min_exp;
            $job->max_exp = $request->max_exp;
            $job->notice = $request->notice;
            $job->status = $request->status;

            // Save the updated job
            $job->save();

            return response()->json(['status' => 'success', 'message' => 'Job updated successfully', 'job' => $job], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'failed', 'error' => 'Failed to update job. Please try again.'], 500);
        }
    }

    public function list(Request $request)
    {
        $sort = $request->input('sort'); // Get the sort parameter from the request body

        if ($sort) {
            // Get all jobs with pagination, sorted by the specified key
            $jobs = Job::where('status', 'active')->orderBy($sort, 'desc')->paginate(10); // Change the number '10' to the desired number of jobs per page
        } else {
            // Get all jobs with pagination
            $jobs = Job::where('status', 'active')->paginate(10); // Change the number '10' to the desired number of jobs per page
        }

        return response()->json(['status' => 'success', 'jobs' => $jobs], 200);
    }

    public function jobDetails($id)
    {
        try {
            // Find the job by its ID
            $job = Job::findOrFail($id);
    
            return response()->json(['status' => 'success', 'job' => $job], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'failed', 'error' => 'Job-not-found.', "message" => $e->getMessage()], 404);
        }
    }

    
    public function updateStatus(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'status' => 'required|in:active,archive',
            ]);
            // Find the job by its ID
            $job = Job::findOrFail($id);
    
            $job->status = $validatedData['status'];
            $job->save();
    
            return response()->json(['status' => 'success', 'message' => 'Job status updated successfully'], 200);
            
        } catch (ValidationException $e) {
            return response()->json(['status' => 'failed', 'errors' => "Status can only either Active or Archive"], 422);
        } catch (Exception $e) {
            // Handle other exceptions
            return response()->json(['status' => 'failed', 'error' => "job not found", "reason" => $e], 500);
        }
        
    }

    public function getJobsByUser(Request $request)
    {
        try {
            // Get the authenticated user
            $user = $request->user(); // Get the authenticated user

            // Retrieve the jobs posted by the user
            $jobs = $user->jobs()->paginate(10); // You can adjust the pagination as per your requirement

            return response()->json(['status' => 'success', 'jobs' => $jobs], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'error' => 'Failed to retrieve jobs.', 'message' => $e], 500);
        }
    }


    public function getRelevantJobsForCandidate(Request $request)
    {
        // Retrieve the currently logged-in candidate's information
        $candidate = $request->user()->candidates()->with('skills')->first();

        if (!$candidate) {
            // Return an appropriate response
            return response()->json(["status" => 'failed', 'error' => 'candidate-information-not-found'], 404);
        }

        // Retrieve relevant jobs based on matching criteria
        $allJobs = Job::where('status', 'active')->with('skills')->get();

        // Implement profile matching algorithm to determine relevance
        $relevantJobs = $this->applyProfileMatchingAlgorithm($allJobs, $candidate);

        // Return the relevant jobs in the decreasing order of relevance
        return response()->json(["status" => 'success', 'status' => 'success', 'jobs' => $relevantJobs], 200);

    }

    private function applyProfileMatchingAlgorithm($jobs, $candidate)
    {
        // Define weights for different criteria
        $skillWeight = 0.6;
        $locationWeight = 0.2;
        $experienceWeight = 0.2;

        // Calculate relevance score for each job based on criteria and weights
        foreach ($jobs as $job) {
            $relevanceScore = 0;

            // Calculate skill relevance score
            $matchedSkills = $job->skills->pluck('id')->intersect($candidate->skills->pluck('id'));
            $skillRelevanceScore = $matchedSkills->count() / $candidate->skills->count();
            $relevanceScore += $skillRelevanceScore * $skillWeight;

            // Calculate location relevance score
            $locationRelevanceScore = ($job->location === $candidate->location) ? 1 : 0;
            $relevanceScore += $locationRelevanceScore * $locationWeight;

            // Calculate experience relevance score
            $experienceRelevanceScore = 1 - abs(($job->min_exp + $job->max_exp) / 2 - $candidate->total_experience) / $candidate->total_experience;
            $relevanceScore += $experienceRelevanceScore * $experienceWeight;

            $job->relevanceScore = $relevanceScore;
        }

        // Sort jobs in descending order of relevance score
        $jobs = $jobs->sortByDesc('relevanceScore')->values()->toArray();

        return $jobs;
    }
}
