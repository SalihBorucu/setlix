<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileIsComplete
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->password_set && !$request->routeIs('profile.complete*')) {
            return redirect()->route('profile.complete');
        }

        if (auth()->check() && !auth()->user()->email_verified_at && !$request->routeIs('profile.complete*')) {
            return redirect()->route('profile.complete');
        }

        return $next($request);
    }
}
