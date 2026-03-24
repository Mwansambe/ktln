<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\SubjectController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - JamExams Mobile Application
|--------------------------------------------------------------------------
|
| All routes here are prefixed with /api
| Authentication uses Laravel Sanctum tokens
|
*/

// ==================== PUBLIC ROUTES (no auth required) ====================
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');
});

// ==================== PROTECTED ROUTES (requires valid token + active account) ====================
Route::middleware(['auth:sanctum', 'check.activation'])->group(function () {

    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('/logout',       [AuthController::class, 'logout'])->name('api.auth.logout');
        Route::get('/me',            [AuthController::class, 'me'])->name('api.auth.me');
        Route::put('/fcm-token',     [AuthController::class, 'updateFcmToken'])->name('api.auth.fcm-token');
    });

    // Subjects
    Route::get('/subjects', [SubjectController::class, 'index'])->name('api.subjects.index');

    // Exams
    Route::prefix('exams')->group(function () {
        Route::get('/',                          [ExamController::class, 'index'])->name('api.exams.index');
        Route::get('/{exam}',                    [ExamController::class, 'show'])->name('api.exams.show');
        Route::get('/{exam}/download',           [ExamController::class, 'download'])->name('api.exams.download');
        Route::get('/{exam}/marking-scheme',     [ExamController::class, 'downloadMarkingScheme'])->name('api.exams.marking-scheme');
    });
});
