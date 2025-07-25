<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, $token = null)
    {
        $type = $request->query('type', 'user');
        return view('auth.reset', [
            'token' => $token,
            'email' => $request->email,
            'type' => $type
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
            'type' => 'required|in:user,owner'
        ]);

        $broker = $request->type === 'owner' ? 'owners' : 'users';

        $status = Password::broker($broker)->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        $redirect = $request->type === 'owner' ? route('owner.login') : route('login');

        return $status === Password::PASSWORD_RESET
            ? redirect($redirect)->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
