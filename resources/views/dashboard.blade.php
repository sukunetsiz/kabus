@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-grid">
        <div class="dashboard-sidebar">
            <div class="dashboard-user-info">
                <div class="dashboard-profile-picture">
                    <img src="{{ $profile ? $profile->profile_picture_url : asset('images/default-profile-picture.png') }}" alt="Profile Picture" class="dashboard-profile-image">
                </div>
                <div class="dashboard-user-details">
                    <p class="dashboard-username">{{ e($user->username) }}</p>
                    <p class="dashboard-user-role">{{ $userRole }}</p>
                    @if($showFullInfo)
                        <p class="dashboard-last-login">Last Login: {{ $user->last_login ? $user->last_login->format('d-m-Y') : 'Never' }}</p>
                    @endif
                </div>
            </div>

            <div class="dashboard-pgp-status">
                <h3 class="dashboard-section-title">PGP Key Status</h3>
                @if($pgpKey)
                    <p>
                        <strong>Status:</strong>
                        @if($pgpKey->verified)
                            <span class="dashboard-pgp-verified">Verified</span>
                        @else
                            <span class="dashboard-pgp-unverified">Unverified</span>
                        @endif
                    </p>
                @else
                    <p>No PGP key added yet.</p>
                @endif
            </div>
        </div>

        <div class="dashboard-main">
            <div class="dashboard-description">
                <h3 class="dashboard-section-title">Profile Description</h3>
                <div class="dashboard-content-wrapper">
                    <p>{!! $description !!}</p>
                </div>
            </div>

            <div class="dashboard-pgp-key-wrapper">
                <div class="dashboard-pgp-key">
                    <h3 class="dashboard-section-title">Current PGP Key</h3>
                    <div class="dashboard-content-wrapper">
                        <div class="dashboard-pgp-key-content">
                            @if($pgpKey)
                                <pre>{{ $pgpKey->public_key }}</pre>
                            @else
                            <div class="text-center">
                                <p>No PGP key added yet.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection