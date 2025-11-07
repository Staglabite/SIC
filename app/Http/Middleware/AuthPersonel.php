<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthPersonel
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('personel')->check()) {
            return redirect('/login')->withErrors(['identifier' => 'Silakan login sebagai Personel.']);
        }
        return $next($request);
    }
}