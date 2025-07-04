@extends('layouts.app')
@section('content')
<div class="auth-container">
    <h2>Login to {{ config('app.name') }}</h2>
    <form class="auth-form" action="{{ route('login') }}" method="POST">
        @csrf
        <div class="form-group text-center">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="{{ old('username') }}" required minlength="4" maxlength="16">
        </div>
        <div class="form-group text-center">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required minlength="8" maxlength="40">
        </div>
        <div class="form-group captcha-group">
            <div class="captcha-container">
                <div class="captcha-label">CAPTCHA</div>
                <img class="captcha-image" src="{{ $captchaImage }}" alt="CAPTCHA Image">
                <input type="text" id="captcha" name="captcha" required minlength="2" maxlength="8">
            </div>
        </div>
        <button type="submit" class="btn btn-submit">Login</button>
    </form>
    <div class="auth-links">
        <a href="{{ route('register') }}" class="auth-link">Create an Account</a>
            <span>|</span>
        <a href="{{ route('password.request') }}" class="auth-link">Forgot Password</a>
    </div>
</div>
@endsection
