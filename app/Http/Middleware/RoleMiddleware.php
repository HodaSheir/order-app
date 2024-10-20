<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure$next, $role): Response
    {
        if (! $user = Auth::guard('api')->user()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Check if the user's role matches the required role
        if (!$user->hasRole($role)) {
            return response()->json(['error' => 'Forbidden'], 403); // No permission
        }

        return $next($request);
    }
}
