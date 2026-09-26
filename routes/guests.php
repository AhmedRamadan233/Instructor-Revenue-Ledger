<?php

use App\Livewire\Guests\Courses;
use App\Livewire\Guests\Home;
use App\Livewire\Guests\Login;
use App\Livewire\Guests\Plans;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
Route::get('/plans', Plans::class)->name('plans');
Route::get('/courses', Courses::class)->name('courses');
Route::get('/login', Login::class)->name('login');
