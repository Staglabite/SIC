<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthPimpinan
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('pimpinan')->check()) {
            return redirect('/login')->withErrors(['identifier' => 'Silakan login sebagai Pimpinan.']);
        }
        return $next($request);
    }
}