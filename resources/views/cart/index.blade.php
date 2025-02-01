@extends('layouts.app')

@section('content')

<div class="cart-index-container">
    {{-- Breadcrumb Navigation --}}
    <div class="cart-index-breadcrumb">
        <a href="{{ route('products.index') }}">Products</a>
        <span>/</span>
        <span>Shopping Cart</span>
    </div>

    {{-- Error Messages --}}
    @if(session('error'))
        <div class="cart-index-alert cart-index-alert-error">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Success Messages --}}
    @if(session('success'))
        <div class="cart-index-alert cart-index-alert-success">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if($cartItems->isEmpty())
        <div class="cart-index-empty-card">
            <h2 class="cart-index-empty-title">Your Cart is Empty</h2>
            <p class="cart-index-empty-text">Browse our products and add items to your cart.</p>
            <a href="{{ route('products.index') }}" class="cart-index-browse-btn">
                Browse Products
            </a>
        </div>
    @else
        <div class="cart-index-main-card">
            {{-- Cart Items --}}
            @foreach($cartItems as $item)
                <div class="cart-index-item">
                    {{-- Product Image --}}
                    <div class="cart-index-item-image">
                        <img src="{{ $item->product->product_picture_url }}" 
                             alt="{{ $item->product->name }}">
                    </div>
                    
                    {{-- Product Details --}}
                    <div class="cart-index-item-details">
                        <div class="cart-index-item-header">
                            <div>
                            <h3 class="cart-index-item-title">
                                {{ $item->product->name }}
                                <span>({{ $item->selected_bulk_option ? ($item->quantity * $item->selected_bulk_option['amount']) : $item->quantity }} {{ $measurementUnits[$item->product->measurement_unit] ?? $item->product->measurement_unit }})</span>
                            </h3>
                                <p class="cart-index-item-vendor">
                                    Sold by: {{ $item->product->user->username }}
                                </p>
                            </div>
                            <div class="cart-index-item-price">
                                <div class="cart-index-price-fiat">
                                    ${{ number_format($item->getTotalPrice(), 2) }}
                                </div>
                                @if(is_numeric($xmrPrice) && $xmrPrice > 0)
                                    <div class="cart-index-price-crypto">
                                        ≈ ɱ{{ number_format($item->getTotalPrice() / $xmrPrice, 4) }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Quantity and Options --}}
                        <div class="cart-index-item-controls">
                            {{-- Quantity Form --}}
                            <form action="{{ route('cart.update', $item) }}" method="POST" class="cart-index-quantity-form">
                                @csrf
                                @method('PUT')
                                <label for="quantity_{{ $item->id }}" class="cart-index-quantity-label">
                                    {{ $item->selected_bulk_option ? 'Number of Sets:' : 'Quantity:' }}
                                </label>
                                <input type="number" 
                                       id="quantity_{{ $item->id }}"
                                       name="quantity" 
                                       value="{{ $item->quantity }}"
                                       min="1"
                                       class="cart-index-quantity-input">
                                <button type="submit" class="cart-index-update-btn">
                                    Update
                                </button>
                            </form>

                            {{-- Delivery Option --}}
                            <div class="cart-index-option-badge">
                                Delivery: {{ $item->selected_delivery_option['description'] }}
                                ({{ $item->selected_delivery_option['price'] > 0 ? '+$' . number_format($item->selected_delivery_option['price'], 2) : 'Free' }})
                            </div>
                        </div>

                        {{-- Additional Controls Container --}}
                        <div class="cart-index-additional-controls {{ $item->selected_bulk_option ? 'cart-index-additional-controls--with-bulk' : '' }}">
                            {{-- Remove Button --}}
                            <form action="{{ route('cart.destroy', $item) }}" method="POST" class="cart-index-remove-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="cart-index-remove-btn">
                                    Remove
                                </button>
                            </form>

                            {{-- Bulk Option --}}
                            @if($item->selected_bulk_option)
                                <div class="cart-index-bulk-badge">
                                    Bulk Deal: {{ $item->quantity }} sets of {{ $item->selected_bulk_option['amount'] }} {{ $measurementUnits[$item->product->measurement_unit] ?? $item->product->measurement_unit }}
                                    at ${{ number_format($item->selected_bulk_option['price'], 2) }} per set
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Bottom Row Container --}}
            <div class="cart-index-bottom-row">
                {{-- Message Section --}}
                <div class="cart-index-message-container">
                    {{-- Show encrypted messages --}}
                    @foreach($cartItems as $item)
                        @if($item->encrypted_message)
                            <div class="cart-index-message-encrypted">
                                <label class="cart-index-message-label">Encrypted Message for {{ $item->product->user->username }}:</label>
                                <textarea readonly>{{ $item->encrypted_message }}</textarea>
                            </div>
                        @endif
                    @endforeach

                    {{-- Show message form only for the first non-encrypted item --}}
                    @php
                        $firstNonEncryptedItem = $cartItems->first(function($item) {
                            return !$item->encrypted_message;
                        });
                    @endphp

                    @if($firstNonEncryptedItem)
                        <form action="{{ route('cart.message.save', $firstNonEncryptedItem) }}" method="POST" class="cart-index-message-form">
                            @csrf
                            <label for="message_{{ $firstNonEncryptedItem->id }}" class="cart-index-message-label">
                                Message for {{ $firstNonEncryptedItem->product->user->username }}:
                            </label>
                            <textarea 
                                id="message_{{ $firstNonEncryptedItem->id }}"
                                name="message" 
                                class="cart-index-message-textarea"
                                placeholder="Enter your message here. It will be encrypted with the vendor's PGP key."
                                required
                            ></textarea>
                            <button type="submit" class="cart-index-message-button">Encrypt & Save Message</button>
                        </form>
                    @endif
                </div>

                {{-- Cart Summary --}}
                <div class="cart-index-summary">
                <div class="cart-index-total">
                    <span class="cart-index-total-label">Total:</span>
                    <div class="cart-index-total-amount">
                        <div class="cart-index-total-fiat">
                            ${{ number_format($cartTotal, 2) }}
                        </div>
                        @if(is_numeric($xmrTotal))
                            <div class="cart-index-total-crypto">
                                ≈ ɱ{{ number_format($xmrTotal, 4) }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="cart-index-actions">
                    {{-- Clear Cart Button --}}
                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="cart-index-clear-btn">
                            Clear Cart
                        </button>
                    </form>

                    {{-- Checkout Button --}}
                    <a href="{{ route('cart.checkout') }}" class="cart-index-checkout-btn">
                        Proceed to Checkout
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
