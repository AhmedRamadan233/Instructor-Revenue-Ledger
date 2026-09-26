<?php

use App\Livewire\Dashboard\Home;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
