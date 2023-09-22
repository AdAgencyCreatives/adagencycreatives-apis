<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdvisorMiddleware
{
    public function handle(Request $request, Closure $next)
    {

        if (! in_array(Auth::user()->role, ['admin', 'advisor'])) { //Allowed for both: advisors and admin
            abort(401);
        }

        return $next($request);
    }
}
