@extends('layouts.app')

@section('content')
@if($popup)
<style>
    /* Popup overlay */
    .popup {
        position: fixed;
        inset: 0;
        background: rgba(0 0 0 / 0.5);
        backdrop-filter: blur(2px);
        place-items: center;
        display: none;
        z-index: 1000;
    }

    /* Popup content */
    .popup-content {
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        max-width: 400px;
        text-align: center;
    }

    /* Close button */
    .close {
        display: inline-block;
        margin-top: 1rem;
        padding: 0.5rem 1rem;
        background: #eee;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .close:hover {
        background: #ddd;
    }

    /* Checkbox hack */
    #popup-toggle { display: none; }
    #popup-toggle:checked ~ .popup { display: grid; }
</style>

<input type="checkbox" id="popup-toggle" checked>
<div class="popup">
    <div class="popup-content">
        <h2>{{ $popup->title }}</h2>
        <p>{{ $popup->message }}</p>
        <form action="{{ route('popup.dismiss') }}" method="POST" style="margin: 0;">
            @csrf
            <input type="hidden" name="dismiss_popup" value="1">
            <button type="submit" class="close">Close</button>
        </form>
    </div>
</div>
@endif

<div class="home-container">
    <div class="home-welcome-message">
        <h1 class="home-title">Welcome to Kabus v0.7.4</h1>
        
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
