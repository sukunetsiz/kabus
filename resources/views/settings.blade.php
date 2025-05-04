@extends('layouts.app')

@section('content')

<div class="container">
    <div class="settings-index-wrapper">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <!-- Top Row: Password and PGP -->
        <div class="settings-index-layout">
            <!-- Change Password Section -->
            <div class="settings-index-change-password-container">
                <div class="settings-index-card">
                    <div class="settings-index-card-header">Need a New Password?</div>
                    <div class="settings-index-card-body text-center">
                        <form method="POST" action="{{ route('settings.changePassword') }}">
                            @csrf
                            <div class="settings-index-page-form-group">
                                <label for="current_password">Current Password</label>
                                <input id="current_password" type="password" class="settings-index-page-form-control @error('current_password') is-invalid @enderror" name="current_password" required autocomplete="current-password">
                                @error('current_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="settings-index-page-form-group">
                                <label for="password">New Password</label>
                                <input id="password" type="password" class="settings-index-page-form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="settings-index-page-form-group">
                                <label for="password_confirmation">Confirm New Password</label>
                                <input id="password_confirmation" type="password" class="settings-index-page-form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                            <button type="submit" class="btn-submit">
                                Change Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- PGP Key Section -->
            <div class="settings-index-manage-pgp-key-container">
                <div class="settings-index-card">
                    <div class="settings-index-card-header">Manage PGP Key</div>
                    <div class="settings-index-card-body text-center">
                        <form method="POST" action="{{ route('settings.updatePgpKey') }}">
                            @csrf
                            <div class="settings-index-page-form-group">
                                <label for="public_key">PGP Public Key</label>
                                <textarea id="public_key" class="settings-index-page-form-control @error('public_key') is-invalid @enderror" name="public_key" rows="10" required>{{ old('public_key', $user->pgpKey->public_key ?? '') }}</textarea>
                                @error('public_key')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <p>You can check the Guides section to learn about PGP.</p>
                            <button type="submit" class="btn-submit">
                                {{ $user->pgpKey ? 'Update PGP Key' : 'Add PGP Key' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Middle Row: Secret Phrase -->
        <div class="settings-index-secret-phrase-row">
            <div class="settings-index-secret-phrase-container">
                <div class="settings-index-card">
                    <div class="settings-index-card-header">Anti‑Phishing Secret Phrase</div>
                    <div class="settings-index-card-body">
                        @if ($user->secretPhrase)
                            <div class="settings-secret-display">
                                <p>Your one‑time secret phrase is:</p>
                                <p class="settings-secret-text">{{ $user->secretPhrase->phrase }}</p>
                                <p>This phrase will always be displayed on your settings page. If you don't see this phrase when logging in, you may be on a phishing site.</p>
                            </div>
                        @else
                            <form class="settings-secret-form" method="POST" action="{{ route('settings.updateSecretPhrase') }}">
                                @csrf
                                <div>
                                    <label for="secret_phrase">Secret Phrase (4‑16 letters, no numbers)</label>
                                    <input id="secret_phrase" type="text" name="secret_phrase" required>
                                    @error('secret_phrase')
                                        <span class="settings-secret-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <p>This is a one‑time setting to help prevent phishing attacks. Your phrase will always be visible on this page.</p>
                                <button type="submit">Set Secret Phrase</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Row: 2FA -->
        <div class="settings-index-manage-2fa-row">
            <div class="settings-index-manage-2fa-container">
                <div class="settings-index-card">
                    <div class="settings-index-card-header">2‑Factor Authentication</div>
                    <div class="settings-index-card-body text-center">
                        @if (!$user->pgpKey || !$user->pgpKey->verified)
                            <p class="text-warning">You need to verify your PGP key to enable 2‑factor authentication.</p>
                        @else
                            <form method="POST" action="{{ route('pgp.2fa.update') }}">
                                @csrf
                                @method('PUT')
                                <div class="settings-index-page-form-group">
                                    <div class="settings-two-fa-buttons">
                                        <button type="submit" name="two_fa_enabled" value="1" class="settings-btn-2fa settings-btn-2fa-on{{ $user->pgpKey->two_fa_enabled ? ' active' : '' }}">ON</button>
                                        <button type="submit" name="two_fa_enabled" value="0" class="settings-btn-2fa settings-btn-2fa-off{{ !$user->pgpKey->two_fa_enabled ? ' active' : '' }}">OFF</button>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
