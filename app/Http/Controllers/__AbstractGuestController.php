<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Attributes\Controllers\Middleware;

#[Middleware('role.guest')]
abstract class __AbstractGuestController extends Controller {}
