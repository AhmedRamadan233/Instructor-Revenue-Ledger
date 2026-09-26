<?php

use App\Livewire\Dashboard\Home;
use App\Livewire\Dashboard\Settings;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
Route::get('settings', Settings::class)->name('settings');
