<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Attributes\Controllers\Middleware;

#[Middleware('auth')]
#[Middleware('teacher')]
abstract class __AbstractTeacherController extends Controller {}
