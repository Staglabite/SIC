<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Menampilkan halaman login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses login (1 pintu untuk 3 role)
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'identifier' => 'required|string',
            'password'   => 'required|string',
        ]);

        $identifier = $request->identifier;

        // 2. Cek Renmin → pakai username
        if (Auth::guard('renmin')->attempt(['username' => $identifier, 'password' => $request->password])) {
            return redirect()->route('renmin.dashboard');
        }

        // 3. Cek Pimpinan → pakai username
        if (Auth::guard('pimpinan')->attempt(['username' => $identifier, 'password' => $request->password])) {
            return redirect()->route('pimpinan.dashboard');
        }

        // 1. Cek Personel → pakai NRP
        if (Auth::guard('personel')->attempt(['nrp' => $identifier, 'password' => $request->password])) {
            return redirect()->route('personel.dashboard');
        }

        // Jika semua gagal
        return back()->withErrors([
            'identifier' => 'NRP / Username atau password salah!'
        ])->withInput();
    }

    // Logout dari semua guard
    public function logout(Request $request)
    {
        if (Auth::guard('personel')->check()) {
            Auth::guard('personel')->logout();
        } elseif (Auth::guard('renmin')->check()) {
            Auth::guard('renmin')->logout();
        } elseif (Auth::guard('pimpinan')->check()) {
            Auth::guard('pimpinan')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}