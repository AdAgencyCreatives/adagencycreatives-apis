<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $resourceType)
    {
        // Allow GET requests without authentication
        if ($request->isMethod('get')) {
            return $next($request);
        }

        // Check for admin role
        if (Auth::user() && Auth::user()->role == 'admin') {
            return $next($request);
        }

        $action = $request->isMethod('post')
            ? 'create'
            : ($request->isMethod('patch')
                ? 'update'
                : ($request->isMethod('delete')
                    ? 'delete'
                    : null));

        if (! $action) {
            return $next($request);
        }

        $permissionName = $resourceType.'.'.$action;

        if (! Auth::user() || ! Auth::user()->hasPermissionTo($permissionName)) {
            return response()->json(['message' => 'Permission denied'], 403);
        }

        return $next($request);
    }
}