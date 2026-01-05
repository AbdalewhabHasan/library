<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Publisher
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'publisher') {
            // Allow access to intended routes
            return $next($request);
        }

        // Redirect only if not already on their dashboard
        return $request->route()->named('publisher.dashboard')
            ? abort(403, 'Access Denied')
            : redirect()->route('publisher.dashboard')->withErrors('Access Denied: Publisher Only');
    }
}
