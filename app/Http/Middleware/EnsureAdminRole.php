<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAdminRole
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !isset(Auth::user()->role) || strtolower((string) Auth::user()->role) !== 'admin') {
            abort(403, 'Access denied. Admin role required.');
        }

        return $next($request);
    }
}
