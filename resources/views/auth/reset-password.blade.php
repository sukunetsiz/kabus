@extends('layouts.app')

@section('content')
<div class="container">
    <div class="auth-container">
        <h2>Reset Password</h2>
            <form method="POST" action="{{ route('password.update') }}" class="auth-form">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="form-group text-center">
                    <label for="password">New Password</label>
                    <input type="password" name="password" id="password" class="form-control" minlength="8" maxlength="40" required>
                </div>
                <div class="form-group text-center">
                    <label for="password_confirmation">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" minlength="8" maxlength="40" required>
                </div>
                <button type="submit" class="btn-submit">
                    Reset Password
                </button>
            </form>
        <div class="auth-links">
            <a href="{{ route('login') }}" class="auth-link">Back to Login</a>
        </div>
    </div>
</div>
@endsection
