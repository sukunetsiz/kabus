@extends('layouts.app')

@section('content')
<div class="container">
    <div class="auth-container">
        <h2>Forgot Password</h2>
            <form method="POST" action="{{ route('password.verify') }}" class="auth-form">
                @csrf
                <div class="form-group text-center">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control" value="{{ old('username') }}" minlength="4" maxlength="16" required autofocus>
                </div>
                <div class="form-group text-center">
                    <label for="mnemonic">12-Word Mnemonic Phrase</label>
                    <input type="text" name="mnemonic" id="mnemonic" class="form-control" value="{{ old('mnemonic') }}" minlength="40" maxlength="512" required>
                </div>
                <button type="submit" class="btn-submit">
                    Verify Mnemonic Phrase
                </button>
            </form>
        <div class="auth-links">
            <a href="{{ route('login') }}" class="auth-link">Back to Login</a>
        </div>
    </div>
</div>
@endsection
