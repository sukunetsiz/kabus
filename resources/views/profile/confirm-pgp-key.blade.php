@extends('layouts.app')

@section('content')
<div class="container">
    <div class="pgp-confirm-container">
        <h1 class="pgp-confirm-title">Confirm PGP Public Key</h1>
        <div class="pgp-confirm-card">
            <div class="pgp-confirm-card-body">
                <h5 class="pgp-confirm-card-title text-center">Encrypted Message</h5>
                <pre class="pgp-confirm-encrypted-message">{{ $encryptedMessage }}</pre>
                <p class="pgp-confirm-instruction">Please decrypt this message using your private key and enter the decrypted message below.</p>
                <form method="POST" action="{{ route('pgp.confirm.submit') }}" class="pgp-confirm-form">
                    @csrf
                    <div class="pgp-confirm-form-group">
                        <label for="decrypted_message" class="pgp-confirm-label text-center">Decrypted Message</label>
                        <textarea name="decrypted_message" id="decrypted_message" class="pgp-confirm-textarea" rows="1" required minlength="16" maxlength="20"></textarea>
                    </div>
                    <div class="pgp-confirm-submit-wrapper">
                        <button type="submit" class="pgp-confirm-submit-btn">Confirm PGP Key</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
