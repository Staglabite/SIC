<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PimpinanController extends Controller
{
    public function index()
    {
        $user = Auth::guard('pimpinan')->user();
        return view('dashboard.pimpinan', compact('user'));
    }
}