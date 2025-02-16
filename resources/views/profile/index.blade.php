@extends('layouts.app')

@section('content')
<div class="container profile-container">
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    @if (session('info'))
        <div class="alert alert-info">
            {{ session('info') }}
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="profile-grid">
            <div class="profile-sidebar">
                <div class="profile-picture-card">
                    <div class="profile-picture-container">
                        <img src="{{ $profile->profile_picture_url }}" alt="Profile Picture" class="profile-picture">
                    </div>
                    <div class="profile-picture-upload">
                        <label for="profile_picture" class="btn btn-primary change-picture-btn">
                            Change Picture
                        </label>
                        <input type="file" name="profile_picture" id="profile_picture" class="profile-picture-input @error('profile_picture') is-invalid @enderror">
                        @error('profile_picture')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <small class="form-text text-muted text-center">Allowed: JPEG, PNG, GIF, WebP. Maximum: 800KB. Will be resized to 160x160px.</small>
                    </div>
                </div>

                <div class="pgp-status-card">
                    <h5 class="card-title text-center pgp-status-title">PGP Key Status</h5>
                    @if(Auth::user()->pgpKey)
                        @if(Auth::user()->pgpKey->verified)
                            <div class="pgp-status verified">
                                <span>Verified PGP Public Key</span>
                            </div>
                        @else
                            <div class="pgp-status unverified">
                                <span>Unverified PGP Public Key</span>
                            </div>
                            <div class="text-center">
                                <a href="{{ route('pgp.confirm') }}" class="btn btn-primary btn-sm confirm-pgp-btn">Verify PGP Public Key</a>
                            </div>
                        @endif
                    @else
                        <div class="pgp-status not-added text-center">
                            <span class="text-danger">No PGP Key Added</span>
                        </div>
                    @endif
                </div>
            </div>
            <div class="profile-main">
                <div class="profile-update-card">
                    <h2 class="card-title text-center">Update Your Profile</h2>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="10">{{ old('description', $profile->description ? e(Crypt::decryptString($profile->description)) : '') }}</textarea>
                        @error('description')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <small class="form-text text-muted text-center">You can write between 4 and 1024 characters. Letters, numbers, spaces and punctuation marks are allowed. Adding a description is required before adding a profile picture.</small>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary update-profile-btn">Update Profile</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection