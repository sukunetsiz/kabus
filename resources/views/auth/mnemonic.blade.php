@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mnemonic-container">
        <h2>Your Mnemonic Phrase</h2>
            <div class="alert alert-error">
                <strong>Warning:</strong> This is the only time you will see this mnemonic phrase. Please write it down and store it securely. You will need this to recover your account if you forget your password.
            </div>

            <div class="mnemonic-display">
                <p class="mnemonic-words">{{ $mnemonic }}</p>
            </div>

            <form method="GET" action="{{ route('login') }}" class="auth-form">
                <button type="submit" class="btn-submit">
                    Continue to Login
                </button>
            </form>
    </div>
</div>
@endsection