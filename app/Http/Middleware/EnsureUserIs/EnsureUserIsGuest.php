<?php

namespace App\Http\Middleware\EnsureUserIs;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsGuest extends __AbstractEnsureUserIsMiddleware
{
    protected function allows(Request $request): bool
    {
        return $request->user() === null;
    }

    protected function deny(Request $request): Response
    {
        return redirect()->route($request->user()->dashboardRouteName());
    }
}
