<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        logger('Request path: ' . $request->path());
        if (
            auth()->check() &&
            auth()->user()->must_change_password &&
            !$request->is('user', 'user/*', 'new_password', 'logout')
        ) {
            return redirect('/user');
        }

        return $next($request);
    }
}
