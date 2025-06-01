<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckTempPassword
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        // If user is a teacher with temp password, redirect to password change
        if ($user && $user->role === 'teacher' && $user->temp_password) {
            // Allow access to password change routes and logout
            if (!$request->routeIs('teacher.password.*') && !$request->routeIs('logout')) {
                return redirect()->route('teacher.password.form');
            }
        }
        
        return $next($request);
    }
}