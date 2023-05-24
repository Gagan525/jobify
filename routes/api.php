<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CandidateInfoController;
use App\Http\Controllers\SkillController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//using for login and rregister
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth')->group(function () {
    //routes accessible only when user logged in 
    Route::group(['middleware' => 'recruiter'], function () {

        // Routes accessible only to recruiters

        //using to post job
        Route::post('job/post', [JobController::class, 'store']);

        //using to update posted job
        Route::put('/job/update/{id}', [JobController::class, 'update']);

        //using to make job status archive or active
        Route::patch('/job/update_status/{id}', [JobController::class, 'updateStatus']);

        //using to see list of job posted by recruiter
        Route::get('/job/posts', [JobController::class, 'getJobsByUser']);

        //using to update job application status (approved/rejeted)
        Route::post('/application/update/{ApplicationId}', [ApplicationController::class, 'updateApplicationStatus']);

        //using to all applications for a job
        Route::get('jobs/applications/{jobId}', [ApplicationController::class, 'listApplicationsForJob']);
    
    });
    Route::group(['middleware' => 'candidate'], function () {
        // Routes accessible only to candidates

        //using to apply in perticular job using its jobId
        Route::post('/job/apply/{jobId}', [ApplicationController::class, 'applyForJob']);

        //using to list all the applied job by authenticated candidate
        Route::get('/job/applied', [ApplicationController::class, 'listApplications']);   
        
        //using to list all the jobs
        Route::get('/jobs', [JobController::class, 'list']);

        //using sort all the jobs in relevance order
        Route::get('/job/relevant', [JobController::class, 'getRelevantJobsForCandidate']);

        //using to get all details of the job using its jobId
        Route::get('/job/{id}', [JobController::class, 'jobDetails']);

        //using to add info of candidate
        Route::post('/profile_info/create', [CandidateInfoController::class, 'store']);

        //using to get candidate profile info
        Route::get('/profile_info', [CandidateInfoController::class, 'getCandidateInfo']);
        
    });

    //using to logout 
    Route::post('logout', [AuthController::class, 'logout']);

    //using to list all skills
    Route::get('/skills', [SkillController::class, 'getAllSkills']);
});