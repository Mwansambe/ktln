<?php

use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ExamController as WebExamController;
use App\Http\Controllers\Web\SubjectController as WebSubjectController;
use App\Http\Controllers\Web\UserController as WebUserController;
use App\Http\Controllers\Web\SettingsController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

// Home route (redirects to dashboard if authenticated, to login if guest)

Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
})->name('home');

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

// Protected routes
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Exams
    Route::prefix('exams')->name('exams.')->group(function () {
        Route::get('/', [WebExamController::class, 'index'])->name('index');
        Route::get('/new', [WebExamController::class, 'create'])->name('create');
        Route::post('/', [WebExamController::class, 'store'])->name('store');
        Route::get('/{exam}', [WebExamController::class, 'show'])->name('show');
        Route::get('/{exam}/edit', [WebExamController::class, 'edit'])->name('edit');
        Route::put('/{exam}', [WebExamController::class, 'update'])->name('update');
        Route::delete('/{exam}', [WebExamController::class, 'destroy'])->name('destroy');
    });
    
    // Subjects/Categories
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [WebSubjectController::class, 'index'])->name('index');
        Route::get('/new', [WebSubjectController::class, 'create'])->name('create');
        Route::post('/', [WebSubjectController::class, 'store'])->name('store');
        Route::get('/{subject}', [WebSubjectController::class, 'show'])->name('show');
        Route::get('/{subject}/edit', [WebSubjectController::class, 'edit'])->name('edit');
        Route::put('/{subject}', [WebSubjectController::class, 'update'])->name('update');
        Route::delete('/{subject}', [WebSubjectController::class, 'destroy'])->name('destroy');
    });
    
    // Users (Admin only)
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [WebUserController::class, 'index'])->name('index');
        Route::get('/new', [WebUserController::class, 'create'])->name('create');
        Route::post('/', [WebUserController::class, 'store'])->name('store');
        Route::get('/{user}', [WebUserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [WebUserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [WebUserController::class, 'update'])->name('update');
        Route::delete('/{user}', [WebUserController::class, 'destroy'])->name('destroy');
    });
    
    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'profile'])->name('profile');
        Route::put('/profile', [SettingsController::class, 'updateProfile'])->name('profile.update');
        Route::get('/notifications', [SettingsController::class, 'notifications'])->name('notifications');
        Route::put('/notifications', [SettingsController::class, 'updateNotifications'])->name('notifications.update');
        Route::get('/security', [SettingsController::class, 'security'])->name('security');
        Route::put('/password', [SettingsController::class, 'updatePassword'])->name('password.update');
    });
});

// Home redirect
Route::get('/', function () {
    return redirect()->route('dashboard');
});
