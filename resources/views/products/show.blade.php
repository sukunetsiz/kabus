@extends('layouts.app')

@section('content')

<div class="products-show-container">
    @if($vendor_on_vacation)
        <div class="products-show-vacation-notice">
            <h2>Product Currently Unavailable</h2>
            <p>This product is temporarily unavailable as the vendor is currently on vacation. Please check back later.</p>
        </div>
    @else
        <div class="products-show-main">
    {{-- Error Messages --}}
    @if(session('error'))
        <div class="alert alert-error">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Success Messages --}}
    @if(session('success'))
        <div class="alert alert-success">
            <p>{{ session('success') }}</p>
        </div>
    @endif
            {{-- Product Name Header --}}
            <div class="products-show-header">
                <h1 class="products-show-title">{{ $product->name }}</h1>
                <div class="products-show-divider"></div>
            </div>

            {{-- Three Column Layout --}}
            <div class="products-show-columns">
                {{-- Left Column (Empty for future use) --}}
                <div class="products-show-column products-show-column-left"></div>

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
        </div>
    @endif
</div>

@endsection
