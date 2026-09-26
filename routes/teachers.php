<?php

use App\Livewire\Teachers\Home;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
