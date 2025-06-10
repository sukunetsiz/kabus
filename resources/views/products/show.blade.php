@extends('layouts.app')

@section('content')

<div class="products-show-container">
    @if($vendor_on_vacation)
        <div class="products-show-vacation-notice">
            <h2>Product Currently Unavailable</h2>
            <p>This product is temporarily unavailable as the vendor is currently on vacation. Please check back later.</p>
        </div>
    @elseif(isset($vendor_shop_private) && $vendor_shop_private)
        <div class="products-show-vacation-notice">
            <h2>Private Shop</h2>
            <p>This product is only available to users who have saved the vendor's reference code. If you have the vendor's reference code, please add it on your References page.</p>
        </div>
    @else
        <div class="products-show-main">
            
            {{-- Product Name Header --}}
            <div class="products-show-header">
                <h1 class="products-show-title">{{ $product->name }}</h1>
                <div class="products-show-divider"></div>
            </div>

            {{-- Three Column Layout --}}
            <div class="products-show-columns">
                {{-- Left Column (Review Statistics) --}}
                <div class="products-show-column products-show-column-left">
                    <div class="products-show-review-stats">
                        <h2 class="products-show-review-stats-header">Review Statistics</h2>
                        
                        {{-- Review Statistics --}}
                        @if($totalReviews > 0)
                            <div class="products-show-review-percentage">
                                <span class="products-show-review-percentage-value">{{ number_format($positivePercentage, 1) }}%</span>
                                <span class="products-show-review-percentage-label">Positive Reviews</span>
                            </div>
                            <div class="products-show-review-counts">
                                <div class="products-show-review-count-item">
                                    <span class="products-show-review-count-label">Positive:</span>
                                    <span class="products-show-review-count-value">{{ $positiveCount }}</span>
                                </div>
                                <div class="products-show-review-count-item">
                                    <span class="products-show-review-count-label">Mixed:</span>
                                    <span class="products-show-review-count-value">{{ $mixedCount }}</span>
                                </div>
                                <div class="products-show-review-count-item">
                                    <span class="products-show-review-count-label">Negative:</span>
                                    <span class="products-show-review-count-value">{{ $negativeCount }}</span>
                                </div>
                                <div class="products-show-review-count-item">
                                    <span class="products-show-review-count-label">Total:</span>
                                    <span class="products-show-review-count-value">{{ $totalReviews }}</span>
                                </div>
                            </div>
                        @else
                            <div class="products-show-review-empty">
                                <p>No reviews yet. Be the first to review this product!</p>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Message to Vendor Button --}}
                    <div class="products-show-message-to-vendor">
                        <a href="{{ route('messages.create', ['username' => $product->user->username]) }}" class="products-show-message-to-vendor-button">
                            Message to Vendor
                        </a>
                    </div>
                </div>

                {{-- Center Column (Product Images) --}}
                <div class="products-show-column-center">
                    <div class="products-show-gallery">
                        <div class="products-show-slider">
                            <div class="products-show-slides">
                                {{-- Main product image --}}
                                <div id="slide-1">
                                    <img src="{{ $product->product_picture_url }}" 
                                         alt="{{ $product->name }}"
                                         class="products-show-gallery-image">
                                </div>

                                {{-- Additional images --}}
                                @if(!empty($product->additional_photos))
                                    @foreach($product->additional_photos_urls as $index => $photoUrl)
                                        <div id="slide-{{ $index + 2 }}">
                                            <img src="{{ $photoUrl }}" 
                                                 alt="{{ $product->name }} - Image {{ $index + 1 }}"
                                                 class="products-show-gallery-image">
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        {{-- Navigation buttons --}}
                        <div class="products-show-slider-navigation">
                            <a href="#slide-1" class="products-show-image-button">M</a>
                            @if(!empty($product->additional_photos))
                                @foreach($product->additional_photos_urls as $index => $photoUrl)
                                    <a href="#slide-{{ $index + 2 }}" class="products-show-image-button">
                                        {{ $index + 1 }}
                                    </a>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Right Column (Product Details) --}}
                <div class="products-show-column products-show-column-right">
                    <div class="products-show-details">
                        <div class="products-show-price">
                            <span class="products-show-price-fiat">${{ number_format($product->price, 2) }}</span>
                            @if(is_numeric($xmrPrice))
                                <span class="products-show-price-monero">≈ ɱ{{ number_format($xmrPrice, 4) }}</span>
                            @endif
                        </div>

                        <div class="products-show-info">
                            <div class="products-show-type">
                                <span class="products-show-badge products-show-badge-{{ $product->type }}">
                                    {{ ucfirst($product->type) }}
                                </span>
                                <span class="products-show-badge products-show-badge-category">
                                    {{ $product->category->name }}
                                </span>
                            </div>

                            <div class="products-show-shipping">
                                <div class="products-show-shipping-badge">
                                    <div class="products-show-shipping-from">From {{ $product->ships_from }}</div>
                                    <div class="products-show-shipping-arrow">⬇</div>
                                    <div class="products-show-shipping-to">To {{ $product->ships_to }}</div>
                                </div>
                            </div>

                            <div class="products-show-stock">
                                <span class="products-show-badge products-show-badge-stock">
                                    Stock ➜ {{ number_format($product->stock_amount) }} {{ $formattedMeasurementUnit }}
                                </span>
                            </div>

                            <div class="products-show-vendor">
                                <div class="products-show-avatar-username">
                                    <div class="products-show-avatar">
                                        <img src="{{ $product->user->profile ? $product->user->profile->profile_picture_url : asset('images/default-profile-picture.png') }}" 
                                             alt="{{ $product->user->username }}'s Profile Picture">
                                    </div>
                                    <div class="products-show-username">
                                        <a href="{{ route('dashboard', $product->user->username) }}" class="products-show-username-link">
                                            {{ $product->user->username }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="products-show-vendor-button-container">
                                <a href="{{ route('vendors.show', $product->user->username) }}" class="products-show-vendor-button">
                                    Visit Vendor
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Wishlist Button Section --}}
            <div class="products-show-wishlist">
                @if(Auth::user()->hasWishlisted($product->id))
                    <form action="{{ route('wishlist.destroy', $product) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="products-show-wishlist-button products-show-wishlist-remove">
                            Remove from Wishlist
                        </button>
                    </form>
                @else
                    <form action="{{ route('wishlist.store', $product) }}" method="POST">
                        @csrf
                        <button type="submit" class="products-show-wishlist-button products-show-wishlist-add">
                            Add to Wishlist
                        </button>
                    </form>
                @endif
            </div>

            {{-- Add to Cart Section --}}
            @if(Auth::id() === $product->user_id)
                <div class="products-show-own-product">
                    <p>You cannot add your own products to the cart.</p>
                </div>
            @else
                <div class="products-show-cart-section">
                    <form action="{{ route('cart.store', $product) }}" method="POST">
                        @csrf
                        <div class="products-show-cart-grid">
                            {{-- Delivery Options --}}
                            <div class="products-show-cart-field">
                                <label for="delivery_option">Delivery Option</label>
                                <select name="delivery_option" id="delivery_option" required>
                                    @foreach($formattedDeliveryOptions as $index => $option)
                                        <option value="{{ $index }}">
                                            {{ $option['description'] }}
                                            @if(str_starts_with($option['price'], '$0.00'))
                                                (Free)
                                            @else
                                                ({{ $option['price'] }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Quantity Input --}}
                            <div class="products-show-cart-field">
                                <label for="quantity">Quantity</label>
                                <input type="number" 
                                       name="quantity" 
                                       id="quantity" 
                                       min="1" 
                                       max="80000"
                                       value="{{ old('quantity', 1) }}" 
                                       required>
                            </div>
                            {{-- Bulk Options --}}
                            @if($product->bulk_options && count($product->bulk_options) > 0)
                                <div class="products-show-cart-field">
                                    <label for="bulk_option">Bulk Purchase Option</label>
                                    <select name="bulk_option" id="bulk_option">
                                        <option value="">Regular Price (No Bulk Discount)</option>
                                        @foreach($formattedBulkOptions as $index => $option)
                                            <option value="{{ $index }}">
                                                {{ $option['display_text'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>

                        <button type="submit" class="products-show-cart-button">
                            Add to Cart
                        </button>
                    </form>
                </div>
            @endif

            {{-- Product Description --}}
            <div class="products-show-description">
                <h2>Product Description</h2>
                <div class="products-show-description-content">
                    {!! nl2br(e($product->description)) !!}
                </div>
            </div>

            {{-- Product Reviews List Section --}}
            @if(count($reviews) > 0)
            <div class="products-show-reviews">
                <h2 class="products-show-reviews-title">Product Reviews</h2>
                
                {{-- Reviews List --}}
                <div class="products-show-reviews-list">
                    @foreach($reviews as $review)
                        <div class="products-show-review-item">
                            <div class="products-show-review-header">
                                <div class="products-show-review-user">
                                    <div class="products-show-review-avatar">
                                        <img src="{{ $review->user->profile ? $review->user->profile->profile_picture_url : asset('images/default-profile-picture.png') }}" 
                                             alt="{{ $review->user->username }}'s Profile Picture">
                                    </div>
                                    <div class="products-show-review-username">{{ $review->user->username }}</div>
                                </div>
                                <div class="products-show-review-meta">
                                    <div class="products-show-review-date">{{ $review->getFormattedDate() }}</div>
                                    <div class="products-show-review-sentiment products-show-review-sentiment-{{ strtolower($review->sentiment) }}">
                                        {{ ucfirst($review->sentiment) }}
                                    </div>
                                </div>
                            </div>
                            <div class="products-show-review-content">
                                {{ $review->review_text }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    @endif
</div>

{{-- Vendor Policy Section --}}
@if(!$vendor_on_vacation && !(isset($vendor_shop_private) && $vendor_shop_private) && $product->user->vendorProfile && $product->user->vendorProfile->vendor_policy)
<div class="products-show-vendor-policy-section">
    <div class="products-show-vendor-policy-container">
        <h2>{{ $product->user->username }}'s Vendor Policy</h2>
        <div class="products-show-vendor-policy-content">
            {!! nl2br(e($product->user->vendorProfile->vendor_policy)) !!}
        </div>
    </div>
</div>
@endif

@endsection
