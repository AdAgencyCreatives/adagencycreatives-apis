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
            if ($request->user()->role == 'admin') {
                return $next($request);
            }
        }

        return app(EnsureFrontendRequestsAreStateful::class)->handle($request, $next);
    }
}
