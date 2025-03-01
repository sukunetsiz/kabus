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
    @if(count($adSlots) > 0)
        <div class="home-advertisement-container">
            @for($i = 1; $i <= 8; $i++)
                @if(isset($adSlots[$i]))
                    <div class="home-advertisement-card">
                        {{-- Product Image --}}
                        <div class="home-advertisement-image">
                            <img src="{{ $adSlots[$i]['product']->product_picture_url }}" 
                                 alt="{{ $adSlots[$i]['product']->name }}">
                        </div>

                        {{-- Product Details --}}
                        <div class="home-advertisement-content">
                            <div class="home-advertisement-header">
                                <div class="home-advertisement-title-section">
                                    <h3 class="home-advertisement-title">{{ $adSlots[$i]['product']->name }}</h3>
                                    <div class="home-advertisement-badges">
                                    <span class="home-advertisement-type home-advertisement-type-{{ $adSlots[$i]['product']->type }}">
                                        {{ ucfirst($adSlots[$i]['product']->type) }}
                                    </span>
                                        <span class="home-advertisement-vendor">
                                            <a href="{{ route('vendors.show', ['username' => $adSlots[$i]['vendor']->username]) }}" class="home-advertisement-vendor-link">
                                                {{ $adSlots[$i]['vendor']->username }}
                                            </a>
                                        </span>
                                        <span class="home-advertisement-badge home-advertisement-category">
                                            {{ $adSlots[$i]['product']->category->name }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="home-advertisement-price-section">
                                    <div class="home-advertisement-price">
                                        ${{ number_format($adSlots[$i]['product']->price, 2) }}
                                    </div>
                                    @if($adSlots[$i]['xmr_price'] !== null)
                                        <div class="home-advertisement-xmr">
                                            ≈ ɱ{{ number_format($adSlots[$i]['xmr_price'], 4) }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="home-advertisement-details">
                                <div class="home-advertisement-info">
                                    <div class="home-advertisement-stock">
                                        {{ number_format($adSlots[$i]['product']->stock_amount) }} 
                                        {{ $adSlots[$i]['measurement_unit'] }}
                                    </div>

                                    <div class="home-advertisement-shipping">
                                        {{ $adSlots[$i]['product']->ships_from }} ➜ {{ $adSlots[$i]['product']->ships_to }}
                                    </div>

                                    @if(!empty($adSlots[$i]['bulk_options']))
                                        <div class="home-advertisement-bulk-preview">
                                            {{ count($adSlots[$i]['bulk_options']) }} Bulk Offers
                                        </div>
                                    @endif

                                        <div class="home-advertisement-delivery-preview">
                                            {{ count($adSlots[$i]['delivery_options']) }} Delivery Options
                                        </div>
                                </div>

                                <div class="home-advertisement-action">
                                    <a href="{{ route('products.show', $adSlots[$i]['product']) }}" 
                                       class="home-advertisement-button">
                                        View Product
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endfor
        </div>
    @endif

    @if(count($adSlots) === 0)
    <div class="home-welcome-message">
        <h1 class="home-title">Welcome to Kabus v0.8.4</h1>
        
        <p class="home-text">Dear users,</p>
        
        <p class="home-text">We are currently in the alpha testing phase. Our marketplace script is not yet fully functional and is not suitable for trading at this time.</p>
        
        <p class="home-text">Project timeline:</p>
        
        <ul class="home-list">
            <li>January 1, 2025: Our introduction phase has begun</li>
            <li>April 4, 2025: Full service launch is planned</li>
        </ul>
        
        <div class="home-important">
            <strong>Important Note</strong>
            <p class="home-text" style="margin-bottom: 0;">Memberships created during this test version should be deleted before the platform launch.</p>
        </div>
        
        <p class="home-text">We kindly ask you to follow our developments closely and thank you for your patience.</p>
        
        <div class="home-signature">
            <p>Best regards,<br>sukunetsiz</p>
        </div>
    </div>
    @endif
</div>
@endsection
