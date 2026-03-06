<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home redirect
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Public routes
Route::middleware('guest')->group(function () {
    // Login routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');;

    // Register routes (optional)
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:5,1');;
});

// Logout route
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// Routes requiring authentication
Route::middleware('auth')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    
    // Dashboard API endpoints
    Route::prefix('api/dashboard')->group(function () {
        Route::get('/statistics', [DashboardController::class, 'getStatistics'])
            ->name('dashboard.statistics');
        Route::get('/recent-users/{limit?}', [DashboardController::class, 'getRecentUsers'])
            ->name('dashboard.recent-users');
        Route::get('/user-distribution', [DashboardController::class, 'getUserDistribution'])
            ->name('dashboard.user-distribution');
        Route::get('/status-distribution', [DashboardController::class, 'getStatusDistribution'])
            ->name('dashboard.status-distribution');
        Route::get('/daily-statistics', [DashboardController::class, 'getDailyStatistics'])
            ->name('dashboard.daily-statistics');
        Route::get('/user-type-by-status', [DashboardController::class, 'getUserTypeByStatus'])
            ->name('dashboard.user-type-by-status');
        Route::get('/export', [DashboardController::class, 'exportData'])
            ->name('dashboard.export');
    });
    
    // Users CRUD
    Route::resource('users', UserController::class);
    
    // Users API endpoints
    Route::prefix('api/users')->group(function () {
        Route::get('/search', [UserController::class, 'search'])
            ->name('users.search');
        Route::get('/by-type/{type}', [UserController::class, 'getByType'])
            ->name('users.by-type');
        Route::patch('/{user}/status', [UserController::class, 'changeStatus'])
            ->name('users.change-status');
        Route::get('/export', [UserController::class, 'export'])
            ->name('users.export');
    });
    
    // Additional routes for specific user types (optional)
    Route::prefix('students')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('students.index');
        Route::get('/create', [UserController::class, 'create'])->name('students.create');
    });
    
    Route::prefix('lecturers')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('lecturers.index');
        Route::get('/create', [UserController::class, 'create'])->name('lecturers.create');
    });
    
    Route::prefix('staff')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('staff.index');
        Route::get('/create', [UserController::class, 'create'])->name('staff.create');
    });

    // Reports routes
    Route::prefix('reports')->group(function () {
        Route::get('/analytics', [App\Http\Controllers\ReportController::class, 'analytics'])
            ->name('reports.analytics');
        
        Route::get('/activity', [App\Http\Controllers\ReportController::class, 'activity'])
            ->name('reports.activity');
    });

    Route::prefix('api/reports')->group(function () {
        Route::get('/analytics', [App\Http\Controllers\ReportController::class, 'getAnalyticsData'])
            ->name('api.reports.analytics');
        
        Route::get('/activity', [App\Http\Controllers\ReportController::class, 'getActivityData'])
            ->name('api.reports.activity');
        
        Route::get('/daily-activity', [App\Http\Controllers\ReportController::class, 'getDailyActivityChart'])
            ->name('api.reports.daily-activity');
    });


    // Settings
    Route::prefix('settings')->group(function () {
        Route::get('/', function() {
            return view('settings.index');
        })->name('settings.index');
    });
});

// Auth routes (if using Laravel UI or Breeze)
// Uncomment if you have authentication scaffolding
// Auth::routes();