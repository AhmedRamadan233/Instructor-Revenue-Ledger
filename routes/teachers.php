<?php

use App\Livewire\Teachers\Allocations;
use App\Livewire\Teachers\Courses;
use App\Livewire\Teachers\CourseStudents;
use App\Livewire\Teachers\Home;
use App\Livewire\Teachers\Ledger;
use App\Livewire\Teachers\Payouts;
use App\Livewire\Teachers\Students;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
Route::get('courses', Courses::class)->name('courses');
Route::get('courses/{course}/students', CourseStudents::class)->name('courses.students');
Route::get('students', Students::class)->name('students');
Route::get('allocations', Allocations::class)->name('allocations');
Route::get('ledger', Ledger::class)->name('ledger');
Route::get('payouts', Payouts::class)->name('payouts');
