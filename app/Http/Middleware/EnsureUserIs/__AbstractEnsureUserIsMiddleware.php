<?php

namespace App\Http\Middleware\EnsureUserIs;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class __AbstractEnsureUserIsMiddleware
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->allows($request)) {
            return $next($request);
        }

        return $this->deny($request);
    }

    abstract protected function allows(Request $request): bool;

    protected function deny(Request $request): Response
    {
        abort(403);
    }
}
