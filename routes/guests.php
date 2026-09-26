<?php

use App\Http\Controllers\Guests\CourseController;
use App\Http\Controllers\Guests\HomeController;
use App\Http\Controllers\Guests\PlanController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/plans', PlanController::class)->name('plans');
Route::get('/courses', CourseController::class)->name('courses');
