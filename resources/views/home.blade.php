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
        <div>
            @for($i = 1; $i <= 8; $i++)
                @if(isset($adSlots[$i]))
                    <div>
                        <div>
                            {{-- Product Image --}}
                            <div>
                                <img src="{{ $adSlots[$i]['product']->product_picture_url }}" 
                                     alt="{{ $adSlots[$i]['product']->name }}">
                            </div>
                        <span>Advertised Product</span>

                            {{-- Product Details --}}
                            <div>
                                {{-- Product Name and Type --}}
                                <h3>{{ $adSlots[$i]['product']->name }}</h3>
                                <div>
                                    <span>{{ ucfirst($adSlots[$i]['product']->type) }}</span>
                                    <span>{{ $adSlots[$i]['product']->category->name }}</span>
                                </div>

                                {{-- Pricing --}}
                                <div>
                                    <p>${{ number_format($adSlots[$i]['product']->price, 2) }}</p>
                                    @if($adSlots[$i]['xmr_price'] !== null)
                                        <p>≈ ɱ{{ number_format($adSlots[$i]['xmr_price'], 4) }}</p>
                                    @endif
                                </div>

                                {{-- Stock Information --}}
                                <div>
                                    <p>Stock: {{ number_format($adSlots[$i]['product']->stock_amount) }} 
                                       {{ $adSlots[$i]['measurement_unit'] }}</p>
                                </div>

                                {{-- Shipping Information --}}
                                <div>
                                    <p>Delivery From: {{ $adSlots[$i]['product']->ships_from }} To: {{ $adSlots[$i]['product']->ships_to }}</p>
                                </div>

                                {{-- Vendor Information --}}
                                <div>
                                    <p>Vendor: {{ $adSlots[$i]['vendor']->username }}</p>
                                </div>

                                {{-- Bulk Options --}}
                                @if(!empty($adSlots[$i]['bulk_options']))
                                    <div>
                                        <p>Bulk Purchase Options:</p>
                                        <ul>
                                            @foreach($adSlots[$i]['bulk_options'] as $option)
                                                <li>{{ $option['display_text'] }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                {{-- Delivery Options --}}
                                <div>
                                    <p>Delivery Options:</p>
                                    <ul>
                                        @foreach($adSlots[$i]['delivery_options'] as $option)
                                            <li>
                                                {{ $option['description'] }} - 
                                                {{ $option['price'] }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                {{-- View Product Link --}}
                                <div class="ad-action">
                                    <a href="{{ route('products.show', $adSlots[$i]['product']) }}">
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
        <h1 class="home-title">Welcome to Kabus v0.8.3</h1>
        
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
