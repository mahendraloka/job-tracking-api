<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobApplicationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('job-applications', JobApplicationController::class);

    Route::get('/dashboard-stats', [JobApplicationController::class, 'getStats']);
});