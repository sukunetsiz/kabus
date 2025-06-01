@extends('layouts.app')
@section('content')
<div class="register-page">
    <div class="auth-container text-center">
        <h2>Register to {{ config('app.name') }}</h2>
        <form class="auth-form" action="{{ route('register') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" required minlength="4" maxlength="16">
                @error('username')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="8" maxlength="40">
                @error('password')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" maxlength="40">
            </div>
            <div class="form-group">
                <label for="reference_code">
                    Reference Code
                    @if(!config('marketplace.require_reference_code', true))
                        <span class="optional-text">(Optional)</span>
                    @endif
                </label>
                <input type="text" 
                       id="reference_code" 
                       name="reference_code" 
                       value="{{ old('reference_code') }}"
                       @if(config('marketplace.require_reference_code', true)) required @endif
                       minlength="12" maxlength="20">
                @error('reference_code')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group captcha-group">
                <div class="captcha-container">
                    <div class="captcha-label">CAPTCHA</div>
                    <img class="captcha-image" src="{{ new Mobicms\Captcha\Image($captchaCode) }}" alt="CAPTCHA Image">
                    <input type="text" id="captcha" name="captcha" required minlength="2" maxlength="8">
                </div>
                @error('captcha')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="btn btn-submit">Register</button>
        </form>
    </div>
</div>
@endsection
