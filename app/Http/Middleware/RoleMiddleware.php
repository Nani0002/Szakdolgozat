<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle role based authorization.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return $request->expectsJson()
                ? response()->json(['error' => 'Not authenticated', 'route' => route('home')], 401)
                : redirect(route('home'));
        }

        $user = Auth::user();

        if (!in_array($user->role, $roles)) {
            return $request->expectsJson()
                ? response()->json(['error' => 'Forbidden'], 403)
                : abort(403);
        }

        return $next($request);
    }
}
