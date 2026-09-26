<?php

namespace App\Http\Controllers\Guests;

use App\Http\Controllers\__AbstractGuestController;
use Illuminate\View\View;

class HomeController extends __AbstractGuestController
{
    public function __invoke(): View
    {
        return view('guests.home');
    }
}
