<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// ==================== AUTHENTICATION ====================
Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ==================== ADMIN PANEL (requires auth) ====================
Route::middleware(['auth', 'verified'])->prefix('app')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Exams (Admin & Editor)
    Route::resource('exams', ExamController::class);
    Route::get('/exams/{exam}/download', [ExamController::class, 'download'])->name('exams.download');

    // Subjects (Admin & Editor)
    Route::resource('subjects', SubjectController::class)->except(['show']);
    Route::get('/categories', [SubjectController::class, 'index'])->name('categories');

    // Users (Admin only)
    Route::resource('users', UserController::class)->except(['edit', 'update']);
    Route::post('/users/{user}/activate',   [UserController::class, 'activate'])->name('users.activate');
    Route::post('/users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');

    // Notifications (Admin only)
    Route::get('/notifications',  [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications', [NotificationController::class, 'send'])->name('notifications.send');

    // Settings
    Route::get('/settings/profile',  [\App\Http\Controllers\Admin\SettingsController::class, 'profile'])->name('settings.profile');
    Route::get('/settings/security', [\App\Http\Controllers\Admin\SettingsController::class, 'security'])->name('settings.security');
    Route::put('/settings/password', [\App\Http\Controllers\Admin\SettingsController::class, 'updatePassword'])->name('settings.password');
});

// Redirect root to dashboard or login
Route::get('/', fn() => redirect()->route('admin.dashboard'));
