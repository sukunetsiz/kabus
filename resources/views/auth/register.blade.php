@extends('layouts.auth')
@section('content')

<div class="auth-register-container">
    <div class="auth-register-inner">
        <h2 class="auth-register-title">Register to {{ config('app.name') }}</h2>
        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="auth-register-form-group">
                <label for="username" class="auth-register-label">Username</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" 
                       class="auth-register-input" required minlength="4" maxlength="16">
            </div>
            <div class="auth-register-form-group">
                <label for="password" class="auth-register-label">Password</label>
                <input type="password" id="password" name="password" 
                       class="auth-register-input" required minlength="8" maxlength="40">
            </div>
            <div class="auth-register-form-group">
                <label for="password_confirmation" class="auth-register-label">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" 
                       class="auth-register-input" required minlength="8" maxlength="40">
            </div>
            <div class="auth-register-form-group">
                <label for="reference_code" class="auth-register-label">
                    Reference Code
                    @if(!config('marketplace.require_reference_code', true))
                        <span class="auth-register-optional-text">(Optional)</span>
                    @endif
                </label>
                <input type="text" 
                       id="reference_code" 
                       name="reference_code" 
                       value="{{ old('reference_code') }}"
                       class="auth-register-input"
                       @if(config('marketplace.require_reference_code', true)) required @endif
                       minlength="12" maxlength="20">
            </div>
            <div class="auth-register-form-group">
                <div class="auth-register-captcha-wrapper">
                    <div class="auth-register-captcha-label">CAPTCHA</div>
                    <img src="{{ $captchaImage }}" alt="CAPTCHA Image" class="auth-register-captcha-image">
                    <input type="text" id="captcha" name="captcha" class="auth-register-input" required minlength="2" maxlength="8">
                </div>
            </div>
            <button type="submit" class="auth-register-submit-btn">Register</button>
        </form>
        <div class="auth-register-links">
            <a href="{{ route('login') }}">Back to Login</a>
        </div>
    </div>
</div>
@endsection
