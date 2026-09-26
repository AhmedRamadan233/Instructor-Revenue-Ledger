<?php

use App\Livewire\Teachers\Allocations;
use App\Livewire\Teachers\Courses;
use App\Livewire\Teachers\Home;
use App\Livewire\Teachers\Ledger;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
Route::get('courses', Courses::class)->name('courses');
Route::get('allocations', Allocations::class)->name('allocations');
Route::get('ledger', Ledger::class)->name('ledger');
