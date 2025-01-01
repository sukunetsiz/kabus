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

        <div class="settings-index-layout">
            <div class="settings-index-change-password-container">
                <div class="settings-index-card-">
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
                            <button type="submit" class="btn btn-primary btn-submit">
                                Change Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="settings-index-manage-pgp-key-container">
                <div class="settings-index-card-">
                    <div class="settings-index-card-header">Add PGP Key</div>
                    <div class="settings-index-card-body text-center">
                        <form method="POST" action="{{ route('settings.updatePgpKey') }}">
                            @csrf
                            <div class="settings-index-page-form-group">
                                <label for="public_key">PGP Public Key</label>
                                <textarea id="public_key" class="settings-index-page-form-control @error('public_key') is-invalid @enderror" name="public_key" rows="10" required>{{ old('public_key') }}</textarea>
                                @error('public_key')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <p>You can check the Guides section to learn about PGP.</p>
                            <button type="submit" class="btn btn-primary btn-submit">
                                Update PGP Key
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="settings-index-manage-2fa-container">
                <div class="settings-index-card-">
                    <div class="settings-index-card-header">2-Factor Authentication</div>
                    <div class="settings-index-card-body text-center">
                        @if (!$user->pgpKey || !$user->pgpKey->verified)
                            <p class="text-warning">You need to verify your PGP key to enable 2-factor authentication.</p>
                        @else
                            <form method="POST" action="{{ route('pgp.2fa.update') }}">
                                @csrf
                                @method('PUT')
                                <div class="settings-index-page-form-group">
                                    <div class="two-fa-buttons">
                                        <button type="submit" name="two_fa_enabled" value="1" class="btn btn-2fa btn-2fa-on{{ $user->pgpKey->two_fa_enabled ? ' active' : '' }}">ON</button>
                                        <button type="submit" name="two_fa_enabled" value="0" class="btn btn-2fa btn-2fa-off{{ !$user->pgpKey->two_fa_enabled ? ' active' : '' }}">OFF</button>
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
