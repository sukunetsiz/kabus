@extends('layouts.app')

@section('content')

<div class="settings-container">
    <div class="settings-grid">
        <div class="settings-card">
            <div class="settings-card-title">Need a New Password?</div>
            <form method="POST" action="{{ route('settings.changePassword') }}">
                @csrf
                <div class="settings-form-group">
                    <label class="settings-form-label" for="current_password">Current Password</label>
                    <input class="settings-form-input" id="current_password" type="password" name="current_password" required minlength="8" maxlength="40" autocomplete="current-password">
                </div>
                <div class="settings-form-group">
                    <label class="settings-form-label" for="password">New Password</label>
                    <input class="settings-form-input" id="password" type="password" name="password" required minlength="8" maxlength="40" autocomplete="new-password">
                </div>
                <div class="settings-form-group">
                    <label class="settings-form-label" for="password_confirmation">Confirm New Password</label>
                    <input class="settings-form-input" id="password_confirmation" type="password" name="password_confirmation" required minlength="8" maxlength="40" autocomplete="new-password">
                </div>
                <button class="settings-button" type="submit">
                    Change Password
                </button>
            </form>
        </div>

        <div class="settings-card">
            <div class="settings-card-title">Manage PGP Key</div>
            <form method="POST" action="{{ route('settings.updatePgpKey') }}">
                @csrf
                <div class="settings-form-group">
                    <label class="settings-form-label" for="public_key">PGP Public Key</label>
                    <textarea class="settings-form-textarea" id="public_key" name="public_key" rows="10" required minlength="100" maxlength="8000">{{ old('public_key', $user->pgpKey->public_key ?? '') }}</textarea>
                </div>
                <p class="settings-info-text">You can check the Guides section to learn about PGP.</p>
                <button class="settings-button" type="submit">
                    {{ $user->pgpKey ? 'Update PGP Key' : 'Add PGP Key' }}
                </button>
            </form>
        </div>

        <div class="settings-card">
            <div class="settings-card-title">Anti‑Phishing Secret Phrase</div>
            @if ($user->secretPhrase)
                <div class="settings-highlight">
                    <p>Your Secret Phrase</p>
                    <p class="settings-phrase">{{ $user->secretPhrase->phrase }}</p>
                    <p>This phrase will always be displayed on your settings page. If you don't see this phrase when logging in, you may be on a phishing site.</p>
                </div>
            @else
                <form method="POST" action="{{ route('settings.updateSecretPhrase') }}">
                    @csrf
                    <div class="settings-form-group">
                        <label class="settings-form-label" for="secret_phrase">Secret Phrase (4‑16 letters, no numbers)</label>
                        <input class="settings-form-input" id="secret_phrase" type="text" name="secret_phrase" required minlength="4" maxlength="16">
                    </div>
                    <p class="settings-info-text">This is a one‑time setting to help prevent phishing attacks. Your phrase will always be visible on this page.</p>
                    <button class="settings-button" type="submit">Set Secret Phrase</button>
                </form>
            @endif
        </div>

        <div class="settings-card">
            <div class="settings-card-title">Account Protection</div>
            @if (!$user->pgpKey || !$user->pgpKey->verified)
                <div class="settings-message settings-message-warning">
                    <p>You need to verify your PGP key to enable 2‑factor authentication.</p>
                </div>
            @else
                <form method="POST" action="{{ route('pgp.2fa.update') }}">
                    @csrf
                    @method('PUT')
                    <label class="settings-form-label" for="two_fa">2‑Factor Authentication</label>
                    <div class="settings-toggle-container">
                        <button class="settings-toggle-button{{ $user->pgpKey->two_fa_enabled ? ' active' : '' }}" type="submit" name="two_fa_enabled" value="1">ON</button>
                        <button class="settings-toggle-button{{ !$user->pgpKey->two_fa_enabled ? ' active' : '' }}" type="submit" name="two_fa_enabled" value="0">OFF</button>
                    </div>
                    <p class="settings-info-text">With 2FA enabled, you'll need your PGP key to decrypt a message during login. This prevents unauthorized access even if your password is compromised.</p>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
