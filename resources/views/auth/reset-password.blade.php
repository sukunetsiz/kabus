@extends('layouts.auth')
@section('content')

<div class="auth-reset-password-container">
    <div class="auth-reset-password-inner">
        <h2 class="auth-reset-password-title">Reset Password</h2>
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="auth-reset-password-form-group">
                <label for="password" class="auth-reset-password-label">New Password</label>
                <input type="password" name="password" id="password" class="auth-reset-password-input" 
                       minlength="8" maxlength="40" required>
            </div>
            <div class="auth-reset-password-form-group">
                <label for="password_confirmation" class="auth-reset-password-label">Confirm New Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="auth-reset-password-input" 
                       minlength="8" maxlength="40" required>
            </div>
            <button type="submit" class="auth-reset-password-submit-btn">
                Reset Password
            </button>
        </form>
        <div class="auth-reset-password-links">
            <a href="{{ route('login') }}">Back to Login</a>
        </div>
    </div>
</div>
@endsection
