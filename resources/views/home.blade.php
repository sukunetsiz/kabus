@extends('layouts.app')

@section('content')
@if($popup)

<input type="checkbox" id="pop-up-toggle" checked>
<div class="pop-up-container">
    <div class="pop-up-card">
        <h2 class="pop-up-title">{{ $popup->title }}</h2>
        <div class="pop-up-content">{{ $popup->message }}</div>
        <form action="{{ route('popup.dismiss') }}" method="POST">
            @csrf
            <input type="hidden" name="dismiss_popup" value="1">
            <button type="submit" class="pop-up-close-btn">
                Acknowledge & Continue
            </button>
        </form>
    </div>
</div>
@endif

<div class="home-container">
    <div class="home-welcome-message">
        <h1 class="home-title">Welcome to Kabus v0.8.2</h1>
        
        <p class="home-text">Dear users,</p>
        
        <p class="home-text">We are currently in the alpha testing phase. Our marketplace script is not yet fully functional and is not suitable for trading at this time.</p>
        
        <p class="home-text">Project timeline:</p>
        
        <ul class="home-list">
            <li>January 1, 2025: Our introduction phase has begun</li>
            <li>April 4, 2025: Full service launch is planned</li>
        </ul>
        
        <div class="home-important">
            <strong>Important Note:</strong>
            <p class="home-text" style="margin-bottom: 0;">Memberships created during this test version should be deleted before the platform launch.</p>
        </div>
        
        <p class="home-text">We kindly ask you to follow our developments closely and thank you for your patience.</p>
        
        <div class="home-signature">
            <p>Best regards,<br>sukunetsiz</p>
        </div>
    </div>
</div>
@endsection
