<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $resourceType)
    {
        return $next($request);

        if (Auth::user()->role == 'admin') {
            return $next($request);
        }

        $action = $request->isMethod('post')
        ? 'create'
        : ($request->isMethod('put')
            ? 'update'
            : ($request->isMethod('delete')
                ? 'delete'
                : null));

        if (! $action) {
            return $next($request);
        }

        $permissionName = $resourceType.'.'.$action;

        if (! Auth::user()->hasPermissionTo($permissionName)) {
            return response()->json(['message' => 'Permission denied'], 403);
        }

        return $next($request);
    }
}
