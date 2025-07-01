@extends('layouts.app')

@section('content')

@if($popup)
<input type="checkbox" id="pop-up-toggle" checked>
<div class="pop-up-container">
    <div class="pop-up-card">
        <h2 class="pop-up-title">{{ $popup->title }}</h2>
        <div class="pop-up-content">{{ $popup->message }}</div>
        <div class="pop-up-button-container">
            <label for="pop-up-toggle" class="pop-up-close-btn">
                Acknowledge & Continue
            </label>
        </div>
    </div>
</div>
@endif

<div class="home-container">
    @if(count($adSlots) > 0)
        <div class="home-highlight-title-wrapper">
            <h2 class="home-highlight-heading">Advertised Products</h2>
        </div>
        <div class="home-highlight-container">
            @for($i = 1; $i <= 8; $i++)
                @if(isset($adSlots[$i]))
                    <div class="home-highlight-card">
                        {{-- Product Image --}}
                        <div class="home-highlight-image">
                            <img src="{{ $adSlots[$i]['product']->product_picture_url }}" 
                                 alt="{{ $adSlots[$i]['product']->name }}">
                        </div>

                        {{-- Product Details --}}
                        <div class="home-highlight-content">
                            <div class="home-highlight-header">
                                <div class="home-highlight-title-section">
                                    <h3 class="home-highlight-title">{{ $adSlots[$i]['product']->name }}</h3>
                                    <div class="home-highlight-badges">
                                    <span class="home-highlight-type home-highlight-type-{{ $adSlots[$i]['product']->type }}">
                                        {{ ucfirst($adSlots[$i]['product']->type) }}
                                    </span>
                                        <span class="home-highlight-vendor">
                                            <a href="{{ route('vendors.show', ['username' => $adSlots[$i]['vendor']->username]) }}" class="home-highlight-vendor-link">
                                                {{ $adSlots[$i]['vendor']->username }}
                                            </a>
                                        </span>
                                        <span class="home-highlight-badge home-highlight-category">
                                            {{ $adSlots[$i]['product']->category->name }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="home-highlight-price-section">
                                    <div class="home-highlight-price">
                                        ${{ number_format($adSlots[$i]['product']->price, 2) }}
                                    </div>
                                    @if($adSlots[$i]['xmr_price'] !== null)
                                        <div class="home-highlight-xmr">
                                            ≈ ɱ{{ number_format($adSlots[$i]['xmr_price'], 4) }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="home-highlight-details">
                                <div class="home-highlight-info">
                                    <div class="home-highlight-stock">
                                        {{ number_format($adSlots[$i]['product']->stock_amount) }} 
                                        {{ $adSlots[$i]['measurement_unit'] }}
                                    </div>

                                    <div class="home-highlight-shipping">
                                        {{ $adSlots[$i]['product']->ships_from }} ➜ {{ $adSlots[$i]['product']->ships_to }}
                                    </div>

                                    @if(!empty($adSlots[$i]['bulk_options']))
                                        <div class="home-highlight-bulk-preview">
                                            {{ count($adSlots[$i]['bulk_options']) }} Bulk Offers
                                        </div>
                                    @endif

                                        <div class="home-highlight-delivery-preview">
                                            {{ count($adSlots[$i]['delivery_options']) }} Delivery Options
                                        </div>
                                </div>

                                <div class="home-highlight-action">
                                    <a href="{{ route('products.show', $adSlots[$i]['product']) }}" 
                                       class="home-highlight-button">
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

    @if(count($featuredProducts) > 0)
        <div class="home-highlight-title-wrapper">
            <h2 class="home-highlight-heading">Featured Products</h2>
        </div>
        <div class="home-highlight-container">
            @foreach($featuredProducts as $featured)
                <div class="home-highlight-card">
                    {{-- Product Image --}}
                    <div class="home-highlight-image">
                        <img src="{{ $featured['product']->product_picture_url }}" 
                             alt="{{ $featured['product']->name }}">
                    </div>

                    {{-- Product Details --}}
                    <div class="home-highlight-content">
                        <div class="home-highlight-header">
                            <div class="home-highlight-title-section">
                                <h3 class="home-highlight-title">{{ $featured['product']->name }}</h3>
                                <div class="home-highlight-badges">
                                <span class="home-highlight-type home-highlight-type-{{ $featured['product']->type }}">
                                    {{ ucfirst($featured['product']->type) }}
                                </span>
                                    <span class="home-highlight-vendor">
                                        <a href="{{ route('vendors.show', ['username' => $featured['vendor']->username]) }}" class="home-highlight-vendor-link">
                                            {{ $featured['vendor']->username }}
                                        </a>
                                    </span>
                                    <span class="home-highlight-badge home-highlight-category">
                                        {{ $featured['product']->category->name }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="home-highlight-price-section">
                                <div class="home-highlight-price">
                                    ${{ number_format($featured['product']->price, 2) }}
                                </div>
                                @if($featured['xmr_price'] !== null)
                                    <div class="home-highlight-xmr">
                                        ≈ ɱ{{ number_format($featured['xmr_price'], 4) }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="home-highlight-details">
                            <div class="home-highlight-info">
                                <div class="home-highlight-stock">
                                    {{ number_format($featured['product']->stock_amount) }} 
                                    {{ $featured['measurement_unit'] }}
                                </div>

                                <div class="home-highlight-shipping">
                                    {{ $featured['product']->ships_from }} ➜ {{ $featured['product']->ships_to }}
                                </div>

                                @if(!empty($featured['bulk_options']))
                                    <div class="home-highlight-bulk-preview">
                                        {{ count($featured['bulk_options']) }} Bulk Offers
                                    </div>
                                @endif

                                    <div class="home-highlight-delivery-preview">
                                        {{ count($featured['delivery_options']) }} Delivery Options
                                    </div>
                            </div>

                            <div class="home-highlight-action">
                                <a href="{{ route('products.show', $featured['product']) }}" 
                                   class="home-highlight-button">
                                    View Product
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if(count($adSlots) === 0 && count($featuredProducts) === 0)
    <div class="home-welcome-message">
        <h1 class="home-title">Welcome to Kabus</h1>
    
        <p class="home-text">Dear users,</p>
    
        <p class="home-text">
            I am excited to announce the official release of Kabus! Our marketplace script is now fully functional and ready for trading.
        </p>
    
        <p class="home-text">
            Thank you for your valuable feedback during the beta phase. It has helped me enhance the platform to better meet your needs.
        </p>
    
        <p class="home-text">What's Next:</p>
    
        <ul class="home-list">
            <li>Continuous updates and new feature integrations</li>
            <li>Your contributions and suggestions are always welcome on our GitHub page (github.com/sukunetsiz/kabus)</li>
        </ul>
    
        <div class="home-important">
            <strong>Security Reminder</strong>
            <p class="home-text">
            Please use this marketplace script with caution. Despite my best efforts, there might be unfound vulnerabilities. I recommend that you do not use this script directly; instead, review and edit it according to your needs. Remember the most important rule of the internet: don't trust, verify.
            </p>
        </div>
    
        <p class="home-text">
            We look forward to growing and evolving together with you. Stay tuned for more updates!
        </p>
    
        <div class="home-signature">
            <p>Best regards,<br>sukunetsiz</p>
        </div>
    </div>
    @endif
</div>
@endsection
