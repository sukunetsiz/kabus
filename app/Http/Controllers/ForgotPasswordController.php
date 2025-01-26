<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function verifyMnemonic(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|string|max:16',
            'mnemonic' => 'required|string|max:512',
        ], [
            'username.required' => 'Please enter your username.',
            'username.max' => 'Username cannot be longer than 16 characters.',
            'mnemonic.required' => 'Please enter your mnemonic phrase.',
            'mnemonic.max' => 'Mnemonic phrase cannot be longer than 512 characters.',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !$this->verifyMnemonicPhrase($request->mnemonic, $user->mnemonic)) {
            return back()->withErrors([
                'error' => 'Username or mnemonic phrase is incorrect.',
            ])->withInput($request->only('username'));
        }

        $token = Str::random(64);
        $user->update([
            'password_reset_token' => Hash::make($token),
            'password_reset_expires_at' => now()->addMinutes(60),
        ]);

        return redirect()->route('password.reset', ['token' => $token])
            ->with('status', 'Please reset your password.');
    }

    private function verifyMnemonicPhrase($providedMnemonic, $storedMnemonic)
    {
        return hash_equals($storedMnemonic, $providedMnemonic);
    }
}