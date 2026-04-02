<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes
Route::middleware('auth:api')->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/verify-email', [AuthController::class, 'verifyEmail']);
        Route::post('/resend-verification', [AuthController::class, 'resendVerification']);
    });

    // User routes
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::put('/{id}/role', [UserController::class, 'updateRole']);
        Route::put('/{id}/toggle-active', [UserController::class, 'toggleActive']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::get('/statistics', [UserController::class, 'statistics']);
    });

    // Subject routes
    Route::prefix('subjects')->group(function () {
        Route::get('/', [SubjectController::class, 'index']);
        Route::get('/paginated', [SubjectController::class, 'paginated']);
        Route::get('/popular', [SubjectController::class, 'popular']);
        Route::get('/top', [SubjectController::class, 'top']);
        Route::get('/with-exams', [SubjectController::class, 'withExams']);
        Route::get('/empty', [SubjectController::class, 'empty']);
        Route::get('/search', [SubjectController::class, 'search']);
        Route::get('/check-name', [SubjectController::class, 'checkName']);
        Route::get('/statistics', [SubjectController::class, 'statistics']);
        Route::get('/{id}', [SubjectController::class, 'show']);
        Route::get('/name/{name}', [SubjectController::class, 'showByName']);
        Route::post('/', [SubjectController::class, 'store']);
        Route::put('/{id}', [SubjectController::class, 'update']);
        Route::delete('/{id}', [SubjectController::class, 'destroy']);
        Route::post('/{id}/recalculate-count', [SubjectController::class, 'recalculateCount']);
        Route::post('/recalculate-all-counts', [SubjectController::class, 'recalculateAllCounts']);
    });

    // Exam routes
    Route::prefix('exams')->group(function () {
        Route::get('/', [ExamController::class, 'index']);
        Route::post('/search', [ExamController::class, 'search']);
        Route::get('/subject/{subjectId}', [ExamController::class, 'bySubject']);
        Route::get('/year/{year}', [ExamController::class, 'byYear']);
        Route::get('/featured', [ExamController::class, 'featured']);
        Route::get('/new', [ExamController::class, 'newExams']);
        Route::get('/most-downloaded', [ExamController::class, 'mostDownloaded']);
        Route::get('/recent', [ExamController::class, 'recent']);
        Route::get('/years/distinct', [ExamController::class, 'distinctYears']);
        Route::get('/subjects/distinct', [ExamController::class, 'distinctSubjects']);
        Route::get('/statistics', [ExamController::class, 'statistics']);
        Route::get('/{id}', [ExamController::class, 'show']);
        Route::get('/code/{code}', [ExamController::class, 'showByCode']);
        Route::get('/{id}/similar', [ExamController::class, 'similar']);
        Route::post('/', [ExamController::class, 'store']);
        Route::put('/{id}', [ExamController::class, 'update']);
        Route::delete('/{id}', [ExamController::class, 'destroy']);
        Route::post('/{id}/marking-scheme', [ExamController::class, 'uploadMarkingScheme']);
        Route::post('/{id}/download', [ExamController::class, 'recordDownload']);
    });

    // Statistics routes
    Route::prefix('statistics')->group(function () {
        Route::get('/dashboard', [StatisticsController::class, 'dashboard']);
        Route::get('/overview', [StatisticsController::class, 'overview']);
    });
});

