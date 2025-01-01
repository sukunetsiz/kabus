@extends('layouts.app')
@section('content')
<div class="auth-container">
    <h2>Login to {{ config('app.name') }}</h2>
    <form class="auth-form" action="{{ route('login') }}" method="POST">
        @csrf
        <div class="form-group text-center">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="{{ old('username') }}" required>
            @error('username')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group text-center">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            @error('password')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group captcha-group">
            <div class="captcha-container">
                <div class="captcha-label">CAPTCHA</div>
                <img class="captcha-image" src="{{ new Mobicms\Captcha\Image($captchaCode) }}" alt="CAPTCHA Image">
                <input type="text" id="captcha" name="captcha" required>
            </div>
            @error('captcha')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit" class="btn btn-submit">Login</button>
    </form>
    <div class="auth-links">
        <a href="{{ route('password.request') }}" class="forgot-password-link">Forgot Password</a>
    </div>
</div>
@endsection
