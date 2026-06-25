<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobApplicationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Rute Publik (Bisa diakses tanpa token)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rute Privat (Wajib membawa Token Sanctum yang valid)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Rute opsional untuk mengecek data user yang sedang login
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Rute Resource API
    Route::apiResource('job-applications', JobApplicationController::class);

    // Jalur khusus untuk menarik ringkasan angka statistik dashboard
    Route::get('/dashboard-stats', [JobApplicationController::class, 'getStats']);

    // Jalur CRUD utama resource lowongan kerja
    Route::apiResource('job-applications', JobApplicationController::class);
});