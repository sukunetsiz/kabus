@extends('layouts.app')

@section('content')
<div class="container">
    <div class="auth-container">
        <h2>Reset Password</h2>
        <div class="card">
            <form method="POST" action="{{ route('password.update') }}" class="auth-form">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                @error('token')
                    <span class="error">{{ $message }}</span>
                @enderror
                <div class="form-group text-center">
                    <label for="password">New Password</label>
                    <input type="password" name="password" id="password" required>
                    @error('password')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group text-center">
                    <label for="password_confirmation">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required>
                    @error('password_confirmation')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn-submit">
                    Reset Password
                </button>
            </form>
        </div>
    </div>
</div>
@endsection