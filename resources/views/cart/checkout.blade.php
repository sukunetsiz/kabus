@extends('layouts.app')

@section('content')
<div class="cart-checkout-container">
    {{-- Breadcrumb Navigation --}}
    <div class="cart-checkout-breadcrumb">
        <a href="{{ route('products.index') }}">Products</a>
        <span>/</span>
        <a href="{{ route('cart.index') }}">Cart</a>
        <span>/</span>
        <span class="cart-checkout-breadcrumb-current">Checkout</span>
    </div>

    {{-- Checkout Details --}}
    <div class="cart-checkout-card">
        <h2 class="cart-checkout-title">Checkout Details</h2>
        
        <div class="cart-checkout-content">
            {{-- Order Details --}}
            <div class="cart-checkout-order-details">
                <div class="cart-checkout-header">
                    <h3 class="cart-checkout-subtitle">Order Information</h3>
                    @if($cartItems->isNotEmpty())
                        <span class="cart-checkout-vendor-badge">Vendor: {{ $cartItems->first()->product->user->username }}</span>
                    @endif
                </div>
                <div class="cart-checkout-items">
                    @if($cartItems->isNotEmpty())
                        @foreach($cartItems as $item)
                            <div class="cart-checkout-item">
                                <div class="cart-checkout-item-details">
                                    <div class="cart-checkout-item-info">
                                        <div class="cart-checkout-item-name">{{ $item->product->name }}</div>
                                        @if($item->selected_bulk_option)
                                            <div class="cart-checkout-item-bulk">
                                                Bulk Deal: {{ $item->quantity }} sets of {{ $item->selected_bulk_option['amount'] }} {{ $measurementUnits[$item->product->measurement_unit] ?? $item->product->measurement_unit }}
                                                (Total: {{ $item->quantity * $item->selected_bulk_option['amount'] }} {{ $measurementUnits[$item->product->measurement_unit] ?? $item->product->measurement_unit }})
                                            </div>
                                        @else
                                            <div class="cart-checkout-item-quantity">
                                                Quantity: {{ $item->quantity }} {{ $measurementUnits[$item->product->measurement_unit] ?? $item->product->measurement_unit }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="cart-checkout-item-price">
                                        <div class="cart-checkout-price-fiat">${{ number_format($item->getTotalPrice(), 2) }}</div>
                                        @if(is_numeric($xmrPrice) && $xmrPrice > 0)
                                            <div class="cart-checkout-price-crypto">
                                                ≈ ɱ{{ number_format($item->getTotalPrice() / $xmrPrice, 4) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Encrypted Message Section --}}
                        @if($hasEncryptedMessage && $messageItem)
                            <div class="cart-checkout-message-section">
                                <div class="cart-checkout-message-encrypted">
                                    <label class="cart-checkout-message-label">
                                        Encrypted Message for {{ $messageItem->product->user->username }}
                                    </label>
                                    <textarea readonly class="cart-checkout-message-content">{{ $messageItem->encrypted_message }}</textarea>
                                </div>
                            </div>
                        @endif
                    @else
                        <p class="cart-checkout-empty">No items in cart.</p>
                    @endif
                </div>
            </div>

            {{-- Price Summary --}}
            <div class="cart-checkout-summary">
                <div class="cart-checkout-summary-card">
                    <h3 class="cart-checkout-summary-title">Price Summary</h3>
                    <div class="cart-checkout-summary-content">
                        <div class="cart-checkout-summary-row">
                            <span>Subtotal</span>
                            <span>${{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="cart-checkout-summary-row">
                            <span>Commission ({{ $commissionPercentage }}%)</span>
                            <span>${{ number_format($commission, 2) }}</span>
                        </div>
                        <div class="cart-checkout-summary-total">
                            <span>Total</span>
                            <div class="cart-checkout-total-amount">
                                <div class="cart-checkout-total-fiat">${{ number_format($total, 2) }}</div>
                                @if(is_numeric($xmrTotal))
                                    <div class="cart-checkout-total-crypto">
                                        ≈ ɱ{{ number_format($xmrTotal, 4) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="cart-checkout-actions">
                    <a href="{{ route('cart.index') }}" class="cart-checkout-back-btn">
                        Back to Cart
                    </a>
                    <form action="{{ route('orders.store') }}" method="POST">
                        @csrf
                        <button type="submit" class="cart-checkout-proceed-btn">
                            Proceed
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
