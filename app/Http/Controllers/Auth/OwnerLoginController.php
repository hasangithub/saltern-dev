<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.owner-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::guard('owner')->attempt($request->only('email', 'password'), $request->filled('remember'))) {
            return redirect()->intended('/productions');
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function logout(Request $request)
    {
        Auth::guard('owner')->logout();
        $request->session()->invalidate();
        return redirect('/owner/login');
    }
}
