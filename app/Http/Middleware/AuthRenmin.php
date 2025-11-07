<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthRenmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('renmin')->check()) {
            return redirect('/login')->withErrors(['identifier' => 'Silakan login sebagai Renmin.']);
        }
        return $next($request);
    }
}