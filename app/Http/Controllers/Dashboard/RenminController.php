<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RenminController extends Controller
{
    public function index()
    {
        $user = Auth::guard('renmin')->user();
        return view('dashboard.renmin', compact('user'));
    }
}