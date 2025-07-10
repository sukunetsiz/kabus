@extends('layouts.auth')
@section('content')
<div class="auth-banned-container">
    <div class="auth-banned-inner">
        <h2 class="auth-banned-title">Your Account Has Been Banned</h2>
        <p class="auth-banned-message">Your account has been temporarily banned for violating our rules.</p>
        <div class="auth-banned-details">
            <p><strong>Reason:</strong> {{ $bannedUser->bannedUser->reason }}</p>
            <p><strong>Until:</strong> {{ $bannedUser->bannedUser->banned_until->format('Y-m-d / H:i:s') }}</p>
        </div>
        <p class="auth-banned-contact">If you think this is a mistake, you can contact us by creating a new account.</p>
        <div class="auth-banned-links">
            <a href="{{ route('login') }}">Return to Login</a>
        </div>
    </div>
</div>
@endsection
