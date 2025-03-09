@extends('layouts.app')

@section('content')
<div class="orders-show-container">
    <div class="orders-show-header">
        <h1 class="orders-show-title">Order Details</h1>
        <div class="orders-show-id">ID: {{ substr($order->id, 0, 8) }}</div>
    </div>

    {{-- Order Status --}}
    <div class="orders-show-status-container">
        <div class="orders-show-status-card orders-show-status-{{ $order->status }}">
            <h2 class="orders-show-status-title">Status: {{ $order->getFormattedStatus() }}</h2>
            
            <div class="orders-show-status-steps {{ $order->status === 'cancelled' ? 'with-cancelled' : '' }}">
                <div class="orders-show-status-step {{ $order->status === 'waiting_payment' || $order->is_paid || $order->is_delivered || $order->is_completed ? 'active' : '' }} {{ $order->status === 'cancelled' && !$order->paid_at ? 'cancelled-step' : '' }}">
                    <div class="orders-show-status-step-number">1</div>
                    <div class="orders-show-status-step-label">Waiting for Payment</div>
                    @if($order->paid_at)
                        <div class="orders-show-status-step-date">{{ $order->paid_at->format('Y-m-d / H:i') }}</div>
                    @endif
                    @if($order->status === 'cancelled' && !$order->paid_at)
                        <div class="orders-show-status-cancelled-marker">
                            <div class="orders-show-status-cancelled-x">X</div>
                        </div>
                    @endif
                </div>
                <div class="orders-show-status-step {{ $order->is_paid || $order->is_delivered || $order->is_completed ? 'active' : '' }} {{ $order->status === 'cancelled' && $order->paid_at && !$order->delivered_at ? 'cancelled-step' : '' }}">
                    <div class="orders-show-status-step-number">2</div>
                    <div class="orders-show-status-step-label">Payment Received</div>
                    @if($order->delivered_at)
                        <div class="orders-show-status-step-date">{{ $order->delivered_at->format('Y-m-d / H:i') }}</div>
                    @endif
                    @if($order->status === 'cancelled' && $order->paid_at && !$order->delivered_at)
                        <div class="orders-show-status-cancelled-marker">
                            <div class="orders-show-status-cancelled-x">X</div>
                        </div>
                    @endif
                </div>
                <div class="orders-show-status-step {{ $order->is_delivered || $order->is_completed ? 'active' : '' }} {{ $order->status === 'cancelled' && $order->delivered_at && !$order->completed_at ? 'cancelled-step' : '' }}">
                    <div class="orders-show-status-step-number">3</div>
                    <div class="orders-show-status-step-label">Product Delivered</div>
                    @if($order->completed_at)
                        <div class="orders-show-status-step-date">{{ $order->completed_at->format('Y-m-d / H:i') }}</div>
                    @endif
                    @if($order->status === 'cancelled' && $order->delivered_at && !$order->completed_at)
                        <div class="orders-show-status-cancelled-marker">
                            <div class="orders-show-status-cancelled-x">X</div>
                        </div>
                    @endif
                </div>
                <div class="orders-show-status-step {{ $order->is_completed ? 'active' : '' }}">
                    <div class="orders-show-status-step-number">4</div>
                    <div class="orders-show-status-step-label">Order Completed</div>
                </div>
            </div>

            {{-- Status-based Actions (without cancel button) --}}
            @if($isBuyer)
                @if($order->status === 'waiting_payment')
                    <div class="orders-show-actions">
                        <form action="{{ route('orders.mark-paid', $order->unique_url) }}" method="POST">
                            @csrf
                            <button type="submit" class="orders-show-action-btn orders-show-payment-done-btn">Payment Done</button>
                        </form>
                    </div>
                @elseif($order->status === 'product_delivered')
                    <div class="orders-show-actions">
                        <form action="{{ route('orders.mark-completed', $order->unique_url) }}" method="POST">
                            @csrf
                            <button type="submit" class="orders-show-action-btn orders-show-confirm-delivery-btn">Confirm Product as Delivered</button>
                        </form>
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- Order Details --}}
    <div class="orders-show-details-container">
        <div class="orders-show-details-card">
            <h2 class="orders-show-details-title">Order Information</h2>
            
            <div class="orders-show-info-grid">
                <div class="orders-show-info-item">
                    <div class="orders-show-info-label">Order Date</div>
                    <div class="orders-show-info-value">{{ $order->created_at->format('Y-m-d / H:i') }}</div>
                </div>
                <div class="orders-show-info-item">
                    <div class="orders-show-info-label">Vendor</div>
                    <div class="orders-show-info-value">{{ $order->vendor->username }}</div>
                </div>
                <div class="orders-show-info-item">
                    <div class="orders-show-info-label">Subtotal</div>
                    <div class="orders-show-info-value">${{ number_format($order->subtotal, 2) }}</div>
                </div>
                <div class="orders-show-info-item">
                    <div class="orders-show-info-label">Commission</div>
                    <div class="orders-show-info-value">${{ number_format($order->commission, 2) }}</div>
                </div>
                <div class="orders-show-info-item">
                    <div class="orders-show-info-label">Total</div>
                    <div class="orders-show-info-value total">${{ number_format($order->total, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Cancel Button Section (new location) --}}
    @if($isBuyer && ($order->status === 'waiting_payment' || $order->status === 'payment_received' || $order->status === 'product_delivered'))
    <div class="orders-show-cancel-container">
        <form action="{{ route('orders.mark-cancelled', $order->unique_url) }}" method="POST">
            @csrf
            <button type="submit" class="orders-show-action-btn orders-show-cancel-btn orders-show-cancel-btn-standalone">Cancel Order</button>
        </form>
    </div>
    @endif

    {{-- Order Items --}}
    <div class="orders-show-items-container">
        <div class="orders-show-items-card">
            <h2 class="orders-show-items-title">Items</h2>
            
            <div class="orders-show-items-list">
                @foreach($order->items as $item)
                    <div class="orders-show-item">
                        <div class="orders-show-item-details">
                            <h3 class="orders-show-item-name">{{ $item->product_name }}</h3>
                            <div class="orders-show-item-description">{{ Str::limit($item->product_description, 200) }}</div>
                            
                            <div class="orders-show-item-meta">
                                @if($item->bulk_option)
                                    <div class="orders-show-item-quantity">
                                        {{ $item->quantity }} sets of {{ $item->bulk_option['amount'] ?? 0 }} 
                                        (Total: {{ $item->quantity * ($item->bulk_option['amount'] ?? 1) }})
                                    </div>
                                @else
                                    <div class="orders-show-item-quantity">
                                        Quantity: {{ $item->quantity }}
                                    </div>
                                @endif
                                
                                @if($item->delivery_option)
                                    <div class="orders-show-item-delivery">
                                        Delivery: {{ $item->delivery_option['description'] ?? 'N/A' }}
                                        ({{ isset($item->delivery_option['price']) ? '$' . number_format($item->delivery_option['price'], 2) : 'N/A' }})
                                    </div>
                                @endif
                                
                                {{-- Display delivery text when order is delivered or completed --}}
                                @if(($order->status === 'product_delivered' || $order->status === 'completed') && $item->delivery_text)
                                    <div class="orders-show-item-delivery-text-container">
                                        <h4>Delivery Information:</h4>
                                        <div class="orders-show-item-delivery-text">
                                            {{ $item->delivery_text }}
                                        </div>
                                    </div>
                                @endif
                                
                                {{-- Display product type pill --}}
                                <div class="orders-show-item-type {{ $item->product->type === 'digital' ? 'type-digital' : ($item->product->type === 'cargo' ? 'type-cargo' : 'type-deaddrop') }}">
                                    @if($item->product->type === 'digital')
                                        Digital
                                    @elseif($item->product->type === 'cargo')
                                        Cargo
                                    @elseif($item->product->type === 'deaddrop')
                                        Dead Drop
                                    @else
                                        {{ ucfirst($item->product->type) }}
                                    @endif
                                </div>
                                {{-- Display category pill --}}
                                <div class="orders-show-item-category">
                                    {{ $item->product->category->name }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="orders-show-item-price">
                            <div class="orders-show-item-price-label">Price:</div>
                            <div class="orders-show-item-price-value">${{ number_format($item->getTotalPrice(), 2) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Message Section --}}
    @if($order->encrypted_message)
        <div class="orders-show-message-container">
            <div class="orders-show-message-card">
                <h2 class="orders-show-message-title">Encrypted Message</h2>
                <div class="orders-show-message-content">
                    <textarea readonly class="orders-show-message-textarea">{{ $order->encrypted_message }}</textarea>
                </div>
            </div>
        </div>
    @endif

    {{-- Reviews Section (Only for completed orders and buyers) --}}
    @if($isBuyer && $order->status === 'completed')
        <div class="orders-show-reviews-container">
            <div class="orders-show-reviews-card">
                <h2 class="orders-show-reviews-title">Product Reviews</h2>
                <div class="orders-show-reviews-list">
                    @foreach($order->items as $item)
                        <div class="orders-show-review-item">
                            <h3 class="orders-show-review-product-name">{{ $item->product_name }}</h3>
                            
                            {{-- Check if already reviewed --}}
                            @if(isset($item->existingReview) && $item->existingReview)
                                <div class="orders-show-review-existing">
                                    <p class="orders-show-review-date">You've already reviewed this product on {{ $item->existingReview->getFormattedDate() }}.</p>
                                    <div class="orders-show-review-sentiment orders-show-review-sentiment-{{ $item->existingReview->sentiment }}">
                                        {{ ucfirst($item->existingReview->sentiment) }}
                                    </div>
                                    <div class="orders-show-review-text">
                                        {{ $item->existingReview->review_text }}
                                    </div>
                                </div>
                            @else
                                <form action="{{ route('orders.submit-review', ['uniqueUrl' => $order->unique_url, 'orderItemId' => $item->id]) }}" method="POST" class="orders-show-review-form">
                                    @csrf
                                    <div class="orders-show-review-field">
                                        <label for="review_text_{{ $item->id }}" class="orders-show-review-label">Your Review</label>
                                        <textarea id="review_text_{{ $item->id }}" name="review_text" required minlength="3" maxlength="1000" placeholder="Write your review here..." class="orders-show-review-textarea"></textarea>
                                    </div>
                                    
                                    <div class="orders-show-review-field">
                                        <label class="orders-show-review-label">Review Sentiment</label>
                                        <div class="orders-show-review-sentiment-options">
                                            <div class="orders-show-review-sentiment-option">
                                                <input type="radio" id="sentiment_positive_{{ $item->id }}" name="sentiment" value="positive" required class="orders-show-review-radio">
                                                <label for="sentiment_positive_{{ $item->id }}" class="orders-show-review-radio-label orders-show-review-sentiment-positive">Positive</label>
                                            </div>
                                            <div class="orders-show-review-sentiment-option">
                                                <input type="radio" id="sentiment_mixed_{{ $item->id }}" name="sentiment" value="mixed" class="orders-show-review-radio">
                                                <label for="sentiment_mixed_{{ $item->id }}" class="orders-show-review-radio-label orders-show-review-sentiment-mixed">Mixed</label>
                                            </div>
                                            <div class="orders-show-review-sentiment-option">
                                                <input type="radio" id="sentiment_negative_{{ $item->id }}" name="sentiment" value="negative" class="orders-show-review-radio">
                                                <label for="sentiment_negative_{{ $item->id }}" class="orders-show-review-radio-label orders-show-review-sentiment-negative">Negative</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="orders-show-review-submit-container">
                                        <button type="submit" class="orders-show-review-submit-btn">Submit Review</button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

@endsection

