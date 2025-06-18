@extends('layouts.app')

@section('content')
<div class="banned-container">
    <div class="banned-card">
        <div class="banned-header">
            <h1 class="banned-title">Your Account Has Been Banned</h1>
        </div>
        <div class="banned-body">
            <p class="banned-message">Your account has been temporarily banned for violating our rules.</p>
            <div class="banned-details text-center">
                <p><strong>Reason:</strong> {{ $bannedUser->bannedUser->reason }}</p>
                <p><strong>Until:</strong> {{ $bannedUser->bannedUser->banned_until->format('Y-m-d H:i:s') }}</p>
            </div>
            <p class="banned-contact">If you think this is a mistake, you can contact us by creating a new account.</p>
        </div>
    </div>
</div>

@endsection
