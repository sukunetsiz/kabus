@extends('layouts.app')
@section('content')

<div class="profile-container">
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="profile-card">
            <div class="profile-grid">
                <div class="profile-sidebar">
                    <div class="profile-picture-container">
                        <div class="profile-picture">
                            <img src="{{ $profile->profile_picture_url }}" alt="Profile Picture">
                        </div>
                        <div class="profile-picture-upload">
                            <label for="profile_picture" class="profile-picture-label">
                                Change Picture
                            </label>
                            <input type="file" name="profile_picture" id="profile_picture" class="profile-picture-input">
                            <small class="profile-picture-hint">Allowed: JPEG, PNG, GIF, WebP. Maximum: 800KB. Will be resized to 160x160px.</small>
                        </div>
                    </div>
                    <div class="profile-pgp-section">
                        <h5 class="profile-pgp-title">PGP Key Status</h5>
                        @if(Auth::user()->pgpKey)
                            @if(Auth::user()->pgpKey->verified)
                                <div class="profile-pgp-status profile-pgp-verified">
                                    <span>Verified PGP Public Key</span>
                                </div>
                            @else
                                <div class="profile-pgp-status profile-pgp-unverified">
                                    <span>Unverified PGP Public Key</span>
                                </div>
                                <div>
                                    <a href="{{ route('pgp.confirm') }}" class="profile-pgp-verify">Verify PGP Public Key</a>
                                </div>
                            @endif
                        @else
                            <div class="profile-pgp-status profile-pgp-none">
                                <span>No PGP Key Added</span>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="profile-form-section">
                    <h2 class="profile-form-title">Update Your Profile</h2>
                    <div class="profile-form-group">
                        <label for="description" class="profile-form-label">Description</label>
                        <textarea name="description" id="description" rows="10" required minlength="4" maxlength="800" class="profile-form-textarea">{{ old('description', $profile->description ? e(Crypt::decryptString($profile->description)) : '') }}</textarea>
                        <small class="profile-form-hint">You can write between 4 and 800 characters. Letters, numbers, spaces and punctuation marks are allowed. Adding a description is required before adding a profile picture.</small>
                    </div>
                    <div>
                        <button type="submit" class="profile-submit-btn">Update Profile</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
