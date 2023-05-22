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

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::group(['middleware' => 'recruiter'], function () {
        // Routes accessible only to recruiters
        Route::post('job/post', [JobController::class, 'store']);
        Route::put('/job/update/{id}', [JobController::class, 'update']);
        Route::patch('/job/update_status/{id}', [JobController::class, 'updateStatus']);
        Route::get('/job/posts', [JobController::class, 'getJobsByUser']);
        Route::post('/application/update/{ApplicationId}', [ApplicationController::class, 'updateApplicationStatus']);
    
    });
    Route::group(['middleware' => 'candidate'], function () {
        // Routes accessible only to candidates
        Route::post('/job/apply/{jobId}', [ApplicationController::class, 'applyForJob']);
        Route::get('/job/applied', [ApplicationController::class, 'listApplications']);        
        Route::get('/jobs', [JobController::class, 'list']);
        Route::get('/job/{id}', [JobController::class, 'jobDetails']);
        Route::post('/profile_info/create', [CandidateInfoController::class, 'store']);
    });
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('/skills', [SkillController::class, 'getAllSkills']);
});