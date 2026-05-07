<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\TargetController;
use App\Http\Controllers\Api\VisitController;
use App\Http\Controllers\Api\WorkdayController;
use Illuminate\Support\Facades\Route;

// Public
Route::post('/login', [AuthController::class, 'login']);

// Authenticated salesperson routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Workday (Start / End day)
    Route::post('/workday/start',  [WorkdayController::class, 'start']);
    Route::post('/workday/end',    [WorkdayController::class, 'end']);
    Route::get('/workday/status',  [WorkdayController::class, 'status']);

    // GPS
    Route::post('/locations/ping',  [LocationController::class, 'ping']);
    Route::post('/locations/batch', [LocationController::class, 'batchPing']);

    // Clients
    Route::get('/clients', [ClientController::class, 'index']);

    // Visits
    Route::get('/visits',                  [VisitController::class, 'index']);
    Route::get('/visits/schedule/today',   [VisitController::class, 'todaySchedule']);
    Route::post('/visits/checkin',         [VisitController::class, 'checkin']);
    Route::post('/visits/log',             [VisitController::class, 'log']);
    Route::put('/visits/{visit}/checkout', [VisitController::class, 'checkout']);
    Route::get('/visits/{visit}',          [VisitController::class, 'show']);

    // Feedback
    Route::get('/feedback/forms',  [FeedbackController::class, 'forms']);
    Route::post('/feedback',       [FeedbackController::class, 'store']);

    // Targets
    Route::get('/targets/my', [TargetController::class, 'my']);
});
