<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StudentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Login / Logout
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');
});
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Tanan nga page sa ubos kinahanglan naka-login
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Attendance Management
    Route::resource('students', StudentController::class);
    Route::get('attendance/take', [AttendanceController::class, 'take'])->name('attendance.take');
    Route::post('attendance/take', [AttendanceController::class, 'saveTake'])->name('attendance.save');
    Route::resource('attendances', AttendanceController::class)->except('show');

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Resource routes para sa Post
    Route::resource('posts', PostController::class);
});
