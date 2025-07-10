@extends('layouts.auth')
@section('content')

<div class="auth-forgot-password-container">
    <div class="auth-forgot-password-inner">
        <h2 class="auth-forgot-password-title">Forgot Password</h2>
        <form method="POST" action="{{ route('password.verify') }}">
            @csrf
            <div class="auth-forgot-password-form-group">
                <label for="username" class="auth-forgot-password-label">Username</label>
                <input type="text" name="username" id="username" class="auth-forgot-password-input" 
                       value="{{ old('username') }}" minlength="4" maxlength="16" required autofocus>
            </div>
            <div class="auth-forgot-password-form-group">
                <label for="mnemonic" class="auth-forgot-password-label">12-Word Mnemonic Phrase</label>
                <textarea name="mnemonic" id="mnemonic" class="auth-forgot-password-input" 
                          minlength="40" maxlength="512" required>{{ old('mnemonic') }}</textarea>
            </div>
            <button type="submit" class="auth-forgot-password-submit-btn">
                Verify Mnemonic Phrase
            </button>
        </form>
        <div class="auth-forgot-password-links">
            <a href="{{ route('login') }}">Back to Login</a>
        </div>
    </div>
</div>
@endsection
