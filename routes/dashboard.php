<?php

use App\Livewire\Dashboard\Courses;
use App\Livewire\Dashboard\Home;
use App\Livewire\Dashboard\Plans;
use App\Livewire\Dashboard\Settings;
use App\Livewire\Dashboard\Teachers;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
Route::get('teachers', Teachers::class)->name('teachers');
Route::get('courses', Courses::class)->name('courses');
Route::get('plans', Plans::class)->name('plans');
Route::get('settings', Settings::class)->name('settings');
