<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExamController;
Route::resource('exams', ExamController::class);
Route::get('/paperchase', function(){

   $message = 'Paperchase is running! Environment: ' . app()->environment();
   $phpVersion = PHP_VERSION;
   $dbConnected = \DB::connection()->getDatabaseName();
return view('welcome', [
    'message'     => $message,
        'phpVersion'  => $phpVersion,
        'dbConnected' => $dbConnected,
]);
})->name('paperchase.status');

Route::resource('subjects', SubjectController::class);





?>
