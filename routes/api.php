<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\TargetController;
use App\Http\Controllers\Api\VisitController;
use Illuminate\Support\Facades\Route;

// Public
Route::post('/login', [AuthController::class, 'login']);

// Authenticated salesperson routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // GPS
    Route::post('/locations/ping',  [LocationController::class, 'ping']);
    Route::post('/locations/batch', [LocationController::class, 'batchPing']);

    // Clients
    Route::get('/clients', [ClientController::class, 'index']);

    // Visits
    Route::get('/visits',                [VisitController::class, 'index']);
    Route::get('/visits/schedule/today', [VisitController::class, 'todaySchedule']);
    Route::post('/visits/checkin',       [VisitController::class, 'checkin']);
    Route::put('/visits/{visit}/checkout', [VisitController::class, 'checkout']);
    Route::get('/visits/{visit}',        [VisitController::class, 'show']);

    // Feedback
    Route::get('/feedback/forms',  [FeedbackController::class, 'forms']);
    Route::post('/feedback',       [FeedbackController::class, 'store']);

    // Targets
    Route::get('/targets/my', [TargetController::class, 'my']);
});
