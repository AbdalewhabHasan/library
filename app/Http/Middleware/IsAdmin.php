<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   
public function handle(Request $request, Closure $next): Response
{
    // Auth::user() يكفي لأنه يُرجع null إذا لم يكن المستخدم مسجل دخوله
    if (Auth::user() && Auth::user()->role === 'admin') {
        return $next($request);
    }

    // إذا لم يكن أدمن، قم بإيقاف الطلب وإظهار صفحة خطأ "غير مصرح به"
    abort(403, 'Unauthorized Action');
}
}