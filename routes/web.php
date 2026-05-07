<?php

use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

// Redirect root to admin login
Route::redirect('/', '/admin/login');

// Fallback for Laravel's default 'login' route redirect (e.g. when unauthenticated)
Route::get('/login', fn() => redirect()->route('admin.login'))->name('login');

// Admin auth (guest)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login',  [Admin\AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [Admin\AuthController::class, 'login'])->name('login.post');
    });

    Route::post('/logout', [Admin\AuthController::class, 'logout'])->name('logout');

    // Protected admin panel
    Route::middleware(['auth', 'admin.role'])->group(function () {
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // Live map
        Route::get('/map',                               [Admin\MapController::class, 'index'])->name('map');
        Route::get('/map/live-data',                     [Admin\MapController::class, 'liveData'])->name('map.live');
        Route::get('/map/rep/{rep}/route',               [Admin\MapController::class, 'repRouteData'])->name('map.rep.route');

        // Salespersons
        Route::get('/salespersons',       [Admin\SalespersonController::class, 'index'])->name('salespersons.index');
        Route::get('/salespersons/{salesperson}', [Admin\SalespersonController::class, 'show'])->name('salespersons.show');

        // Clients
        Route::get('/clients',            [Admin\ClientController::class, 'index'])->name('clients.index');
        Route::get('/clients/create',     [Admin\ClientController::class, 'create'])->name('clients.create');
        Route::post('/clients',           [Admin\ClientController::class, 'store'])->name('clients.store');
        Route::get('/clients/{client}/edit', [Admin\ClientController::class, 'edit'])->name('clients.edit');
        Route::put('/clients/{client}',   [Admin\ClientController::class, 'update'])->name('clients.update');

        // Visits
        Route::get('/visits',             [Admin\VisitController::class, 'index'])->name('visits.index');
        Route::get('/visits/{visit}',     [Admin\VisitController::class, 'show'])->name('visits.show');

        // Feedback
        Route::get('/feedback',           [Admin\FeedbackController::class, 'index'])->name('feedback.index');
        Route::get('/feedback/{feedback}',[Admin\FeedbackController::class, 'show'])->name('feedback.show');

        // Targets
        Route::get('/targets',            [Admin\TargetController::class, 'index'])->name('targets.index');
        Route::post('/targets',           [Admin\TargetController::class, 'store'])->name('targets.store');

        // Reports
        Route::get('/reports',            [Admin\ReportController::class, 'index'])->name('reports.index');

        // User management (superadmin only)
        Route::get('/users',              [Admin\UserController::class, 'index'])->name('users.index');
        Route::get('/users/create',       [Admin\UserController::class, 'create'])->name('users.create');
        Route::post('/users',             [Admin\UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit',  [Admin\UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}',       [Admin\UserController::class, 'update'])->name('users.update');

        // Territories
        Route::get('/territories',                    [Admin\TerritoryController::class, 'index'])->name('territories.index');
        Route::get('/territories/create',             [Admin\TerritoryController::class, 'create'])->name('territories.create');
        Route::post('/territories',                   [Admin\TerritoryController::class, 'store'])->name('territories.store');
        Route::get('/territories/{territory}/edit',   [Admin\TerritoryController::class, 'edit'])->name('territories.edit');
        Route::put('/territories/{territory}',        [Admin\TerritoryController::class, 'update'])->name('territories.update');
        Route::delete('/territories/{territory}',     [Admin\TerritoryController::class, 'destroy'])->name('territories.destroy');
    });
});
