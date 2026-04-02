<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('app');
// })->name('app');

//  Route::get('/', function () {
//     return view('home');
// })->name('app');

//Route::view('/', 'home')->name('home');
Route::view('/', 'home')->name('home');
Route::view('/welcome', 'welcome')->name('welcome');
Route::view('/contact', 'pages.contact')->name('contact');

