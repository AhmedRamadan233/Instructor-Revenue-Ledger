<?php

use App\Livewire\Students\Home;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
