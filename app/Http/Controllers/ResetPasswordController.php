<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function reset(ResetPasswordRequest $request)
    {
        $user = User::where('password_reset_expires_at', '>', now())->get()
            ->first(function ($user) use ($request) {
                return Hash::check($request->token, $user->password_reset_token);
            });

        if (!$user) {
            return back()->withErrors(['error' => 'This password reset token is invalid or has expired.']);
        }

        $user->password = Hash::make($request->password);
        $user->password_reset_token = null;
        $user->password_reset_expires_at = null;
        $user->save();

        return redirect()->route('login')
            ->with('status', 'Your password has been successfully reset. You can now login with your new password.');
    }
}