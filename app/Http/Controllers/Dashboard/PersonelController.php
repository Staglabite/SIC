<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PersonelController extends Controller
{
    public function index()
    {
        $user = Auth::guard('personel')->user();
        return view('dashboard.personel', compact('user'));
    }
}