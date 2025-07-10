@extends('layouts.auth')
@section('content')

<div class="auth-login-container">
    <div class="auth-login-inner">
        <h2 class="auth-login-title">Login to {{ config('app.name') }}</h2>
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="auth-login-form-group">
                <label for="username" class="auth-login-label">Username</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" 
                       class="auth-login-input" required minlength="4" maxlength="16">
            </div>
            <div class="auth-login-form-group">
                <label for="password" class="auth-login-label">Password</label>
                <input type="password" id="password" name="password" 
                       class="auth-login-input" required minlength="8" maxlength="40">
            </div>
            <div class="auth-login-form-group">
                <div class="auth-login-captcha-wrapper">
                    <div class="auth-login-captcha-label">CAPTCHA</div>
                    <img src="{{ new Mobicms\Captcha\Image($captchaCode) }}" alt="CAPTCHA Image" class="auth-login-captcha-image">
                    <input type="text" id="captcha" name="captcha" class="auth-login-input" required minlength="2" maxlength="8">
                </div>
            </div>
            <button type="submit" class="auth-login-submit-btn">Login</button>
        </form>
        <div class="auth-login-links">
            <a href="{{ route('register') }}">Create an Account</a>
            <span class="auth-login-links-separator">|</span>
            <a href="{{ route('password.request') }}">Forgot Password</a>
        </div>
    </div>
</div>
@endsection
