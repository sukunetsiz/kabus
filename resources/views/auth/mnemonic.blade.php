@extends('layouts.auth')
@section('content')

<div class="auth-mnemonic-container">
    <div class="auth-mnemonic-inner">
        <h2 class="auth-mnemonic-title">Your Mnemonic Phrase</h2>
        
        <div class="auth-mnemonic-warning">
            <strong>Warning:</strong>
            <div class="auth-mnemonic-warning-text">
                This is the only time you will see this mnemonic phrase. Please write it down and store it securely. You will need this to recover your account if you forget your password.
            </div>
        </div>
        
        <div class="auth-mnemonic-display">
            <p class="auth-mnemonic-words">{{ $mnemonic }}</p>
        </div>
        
        <form method="GET" action="{{ route('login') }}" class="auth-mnemonic-form">
            <button type="submit" class="auth-mnemonic-submit-btn">
                Continue to Login
            </button>
        </form>
    </div>
</div>
@endsection
