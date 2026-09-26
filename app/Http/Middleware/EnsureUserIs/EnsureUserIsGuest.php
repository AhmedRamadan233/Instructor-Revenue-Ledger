<?php

namespace App\Http\Middleware\EnsureUserIs;

use Illuminate\Http\Request;

class EnsureUserIsGuest extends __AbstractEnsureUserIsMiddleware
{
    protected function allows(Request $request): bool
    {
        return $request->user() === null;
    }
}
