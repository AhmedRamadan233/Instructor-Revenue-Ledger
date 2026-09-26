<?php

use App\Livewire\Students\Courses;
use App\Livewire\Students\Home;
use App\Livewire\Students\Subscriptions;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
Route::get('subscriptions', Subscriptions::class)->name('subscriptions');
Route::get('courses', Courses::class)->name('courses');
