<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function showRequestForm($type = 'user')
    {
        return view("auth.email", compact('type'));
    }

    public function sendLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'type' => 'required|in:user,owner'
        ]);

        $broker = $request->type === 'owner' ? 'owners' : 'users';

        $status = Password::broker($broker)->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}

