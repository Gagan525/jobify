<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class ApplicationController extends Controller
{
    //

    public function applyForJob(Request $request, $jobId)
    {
        try {
            $user = $request->user(); // Get the authenticated user

            // Create a new application
            $application = new Application();
            $application->jobId = $jobId;
            $application->candidateId = $user->id;
            $application->save();

            return response()->json(['status' => 'success', 'message' => 'Application submitted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'error' => 'Job not found'], 404);
        } catch (ValidationException $e) {
            return response()->json(['status' => 'failed', 'error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['status' => 'failed', 'error' => 'An error occurred'], 500);
        }
    }

    public function updateApplicationStatus(Request $request, $applicationId)
    {
        try {
            $validatedData = $request->validate([
                'status' => 'required|in:approved,rejected',
            ]);
            // Find the application by its ID
            $application = Application::findOrFail($applicationId);
    
            $application->status = $validatedData['status'];
            $application->save();
    
            return response()->json(['status' => 'success', 'message' => 'Application status updated successfully'], 200);
            
        } catch (ValidationException $e) {
            return response()->json(['status' => 'failed', 'error' => "Status can only either Approved or Rejected"], 422);
        } catch (Exception $e) {
            // Handle other exceptions
            return response()->json(['status' => 'failed', 'error' => "Application not found", "reason" => $e], 500);
        }
    }

    public function listApplications(Request $request)
    {
        try {
            // Get the authenticated user
            $user = $request->user();

            // Retrieve the jobs posted by the user
            $user = $request->user(); // Get the authenticated user

            // Retrieve applications for the logged-in user with pagination
            $applications = Application::where('candidateId', $user->id)
            ->with('job') // Eager load the job details
            ->paginate(10); // Change the value '10' to set the number of items per page

            // Customize the pagination response
            // Paginator::useBootstrap();

            return response()->json(['status' => 'success', 'applications' => $applications], 200);

        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'error' => 'Failed to retrieve applications.', 'message' => $e->getMessage()], 500);
        }
    }
}
