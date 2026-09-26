<?php

namespace App\Http\Middleware\EnsureUserIs;

use Illuminate\Http\Request;

class EnsureUserIsManager extends __AbstractEnsureUserIsMiddleware
{
    protected function allows(Request $request): bool
    {
        return (bool) $request->user()?->is_manager;
    }
}
