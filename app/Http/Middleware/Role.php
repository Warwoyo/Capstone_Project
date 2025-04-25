<?php

// app/Http/Middleware/Role.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Role
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (! in_array(Auth::user()->role ?? '', $roles)) {
            abort(403);
        }
        return $next($request);
    }
}
