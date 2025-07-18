@extends('layouts.auth')
@section('content')
<div class="auth-two-fa-container">
    <div class="auth-two-fa-inner">
        <h1 class="auth-two-fa-title">2-Step PGP Verification</h1>
        <div class="auth-two-fa-content">
            <div class="auth-two-fa-message-section">
                <h5 class="auth-two-fa-message-title">Encrypted Message</h5>
                <pre class="auth-two-fa-encrypted-message">{{ $encryptedMessage }}</pre>
                <p class="auth-two-fa-instruction">Please decrypt this message using your private key and enter the decrypted message below.</p>
            </div>
            
            <form method="POST" action="{{ route('pgp.2fa.verify') }}" class="auth-two-fa-form">
                @csrf
                <div class="auth-two-fa-form-group">
                    <label for="decrypted_message" class="auth-two-fa-label">Decrypted Message</label>
                    <textarea name="decrypted_message" id="decrypted_message" rows="1" required autocomplete="off" class="auth-two-fa-textarea"></textarea>
                </div>
                <div class="auth-two-fa-submit-group">
                    <button type="submit" class="auth-two-fa-submit-btn">Complete 2-Step PGP Verification</button>
                </div>
                <div class="auth-two-fa-links">
                    <a href="{{ route('login') }}">Back to Login</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
