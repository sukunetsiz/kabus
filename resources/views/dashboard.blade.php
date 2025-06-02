@extends('layouts.app')
@section('content')

<div class="dashboard-container">
    <div class="dashboard-grid">
        <!-- Profile Information Card -->
        <div class="dashboard-card dashboard-profile-card">
            <div class="dashboard-profile-header">
                <div class="dashboard-profile-image-container">
                    <img class="dashboard-profile-image" src="{{ $profile ? $profile->profile_picture_url : asset('images/default-profile-picture.png') }}" alt="Profile Picture">
                </div>
                <h2 class="dashboard-profile-name">{{ e($user->username) }}</h2>
                <p class="dashboard-profile-role">{{ $userRole }}</p>
                @if($showFullInfo)
                    <p class="dashboard-profile-last-login">Last Login: {{ $user->last_login ? $user->last_login->format('d-m-Y') : 'Never' }}</p>
                @endif
            </div>
            
            <h3 class="dashboard-card-title">PGP Key Status</h3>
            <div class="dashboard-pgp-status">
                @if($pgpKey)
                    @if($pgpKey->verified)
                        <span class="dashboard-pgp-badge dashboard-pgp-verified">Verified</span>
                    @else
                        <span class="dashboard-pgp-badge dashboard-pgp-unverified">Unverified</span>
                    @endif
                @else
                    <span class="dashboard-pgp-badge dashboard-pgp-none">No PGP Key</span>
                @endif
            </div>
        </div>

        <!-- Profile Description and PGP Key Card -->
        <div>
            <!-- Profile Description -->
            <div class="dashboard-card">
                <h3 class="dashboard-card-title">Profile Description</h3>
                <div class="dashboard-description">
                    <p>{!! $description !!}</p>
                </div>
            </div>

            <!-- Current PGP Key -->
            <div class="dashboard-card" style="margin-top: 30px;">
                <h3 class="dashboard-card-title">Current PGP Key</h3>
                <div class="dashboard-pgp-key-container">
                    <div class="dashboard-pgp-key">
                        @if($pgpKey)
                            <pre>{{ $pgpKey->public_key }}</pre>
                        @else
                            <div class="dashboard-pgp-empty">
                                <p>No PGP key added yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
