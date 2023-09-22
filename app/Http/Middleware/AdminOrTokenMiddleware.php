<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

class AdminOrTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()) {
            if (in_array($request->user()->role, ['admin', 'advisor'])) {
                return $next($request);
            }
        }

        return app(EnsureFrontendRequestsAreStateful::class)->handle($request, $next);
    }
}
