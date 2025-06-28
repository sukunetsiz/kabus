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
            
            <div class="orders-show-status-steps {{ $order->status === 'cancelled' ? 'with-cancelled' : '' }} {{ isset($dispute) && $dispute ? 'with-disputed' : '' }}">
                <div class="orders-show-status-step {{ $order->status === 'waiting_payment' || $order->is_paid || $order->is_sent || $order->is_completed ? 'active' : '' }} {{ $order->status === 'cancelled' && !$order->paid_at ? 'cancelled-step' : '' }}">
                    <div class="orders-show-status-step-number">1</div>
                    <div class="orders-show-status-step-label">Waiting for Payment</div>
                    @if($order->created_at)
                        <div class="orders-show-status-step-date">{{ $order->created_at->format('Y-m-d / H:i') }}</div>
                    @endif
                    @if($order->status === 'cancelled' && !$order->paid_at)
                        <div class="orders-show-status-cancelled-marker">
                            <div class="orders-show-status-cancelled-x">X</div>
                        </div>
                    @endif
                </div>
                <div class="orders-show-status-step {{ $order->is_paid || $order->is_sent || $order->is_completed ? 'active' : '' }} {{ $order->status === 'cancelled' && $order->paid_at && !$order->sent_at ? 'cancelled-step' : '' }}">
                    <div class="orders-show-status-step-number">2</div>
                    <div class="orders-show-status-step-label">Payment Received</div>
                    @if($order->paid_at)
                        <div class="orders-show-status-step-date">{{ $order->paid_at->format('Y-m-d / H:i') }}</div>
                    @endif
                    @if($order->status === 'cancelled' && $order->paid_at && !$order->sent_at)
                        <div class="orders-show-status-cancelled-marker">
                            <div class="orders-show-status-cancelled-x">X</div>
                        </div>
                    @endif
                </div>
                <div class="orders-show-status-step {{ $order->is_sent || $order->is_completed ? 'active' : '' }} {{ $order->status === 'cancelled' && $order->sent_at && !$order->completed_at ? 'cancelled-step' : '' }}">
                    <div class="orders-show-status-step-number">3</div>
                    <div class="orders-show-status-step-label">Product Sent</div>
                    @if($order->sent_at)
                        <div class="orders-show-status-step-date">{{ $order->sent_at->format('Y-m-d / H:i') }}</div>
                    @endif
                    @if($order->status === 'cancelled' && $order->sent_at && !$order->completed_at)
                        <div class="orders-show-status-cancelled-marker">
                            <div class="orders-show-status-cancelled-x">X</div>
                        </div>
                    @endif
                    @if(isset($dispute) && $dispute)
                        <div class="orders-show-status-disputed-marker">
                            <div class="orders-show-status-disputed-question">?</div>
                        </div>
                    @endif
                </div>
                <div class="orders-show-status-step {{ $order->is_completed ? 'active' : '' }}">
                    <div class="orders-show-status-step-number">4</div>
                    <div class="orders-show-status-step-label">Order Completed</div>
                    @if($order->completed_at)
                        <div class="orders-show-status-step-date">{{ $order->completed_at->format('Y-m-d / H:i') }}</div>
                    @endif
                </div>
            </div>

            {{-- Status-based Actions (without cancel button) --}}
            @if($isBuyer)
                @if($order->status === 'waiting_payment')
                @elseif($order->status === 'payment_received')
                    <div class="orders-show-status-explanation">
                        <p>This order will be automatically cancelled if the vendor does not mark it as sent within 
                        <strong>96 hours (4 days)</strong> after payment was received.</p>
                        
                        @if($order->getAutoCancelDeadline())
                            <p>Auto-cancel deadline: <strong>{{ $order->getAutoCancelDeadline()->format('Y-m-d H:i') }}</strong> 
                            ({{ $order->getAutoCancelDeadline()->diffForHumans() }})</p>
                        @endif
                    </div>
                @elseif($order->status === 'product_sent')
                    <div class="orders-show-actions">
                        <form action="{{ route('orders.mark-completed', $order->unique_url) }}" method="POST" class="orders-show-action-form">
                            @csrf
                            <button type="submit" class="orders-show-action-btn orders-show-confirm-delivery-btn">Confirm Order</button>
                        </form>
                    </div>
                    
                    @if(!$order->is_disputed)
                        <div class="orders-show-status-explanation">
                            <p>This order will be automatically marked as completed if not confirmed within 
                            <strong>192 hours (8 days)</strong> after being marked as sent.</p>
                            
                            @if($order->getAutoCompleteDeadline())
                                <p>Auto-complete deadline: <strong>{{ $order->getAutoCompleteDeadline()->format('Y-m-d H:i') }}</strong> 
                                ({{ $order->getAutoCompleteDeadline()->diffForHumans() }})</p>
                            @endif
                        </div>
                    @endif
                @endif
            @endif
        </div>
    </div>

        {{-- Monero Payment Section (for waiting_payment status) --}}
    @if($isBuyer && $order->status === 'waiting_payment')
        <div class="orders-show-payment-container">
            <div class="orders-show-payment-card">
                <h2 class="orders-show-payment-subtitle">Payment Information</h2>
            
                <div class="orders-show-payment-details">
                    <div class="orders-show-payment-row">
                        <span class="orders-show-payment-label">Required Amount:</span>
                        <span class="orders-show-payment-value">
                            <span class="orders-show-payment-amount">ɱ{{ number_format($order->required_xmr_amount, 12) }} XMR</span>
                        </span>
                    </div>
                
                    <div class="orders-show-payment-row">
                        <span class="orders-show-payment-label">USD/XMR Rate:</span>
                        <span class="orders-show-payment-value">
                            ${{ number_format($order->xmr_usd_rate, 2) }} per XMR
                        </span>
                    </div>
                
                    <div class="orders-show-payment-row">
                        <span class="orders-show-payment-label">Minimum Payment:</span>
                        <span class="orders-show-payment-value">
                            <span class="orders-show-payment-amount">ɱ{{ number_format($order->required_xmr_amount * 0.1, 12) }} XMR (10%)</span>
                        </span>
                    </div>
                
                    @if($order->total_received_xmr > 0 && !$order->is_paid)
                        <div class="orders-show-payment-row">
                            <span class="orders-show-payment-label">Amount Received:</span>
                            <div class="orders-show-payment-value-group">
                                <span class="orders-show-payment-amount">ɱ{{ number_format($order->total_received_xmr, 12) }} XMR</span>
                                <span class="orders-show-payment-remaining">
                                    Remaining: ɱ{{ number_format($order->required_xmr_amount - $order->total_received_xmr, 12) }} XMR
                                </span>
                            </div>
                        </div>
                    @endif
                
                    <div class="orders-show-payment-row">
                        <span class="orders-show-payment-label">Payment Status:</span>
                        <div class="orders-show-payment-status-wrapper">
                            @if($order->is_paid)
                                <span class="orders-show-payment-status orders-show-payment-status-completed">
                                    Payment Completed
                                </span>
                            @elseif($order->total_received_xmr > 0 && $order->total_received_xmr < $order->required_xmr_amount)
                                <span class="orders-show-payment-status orders-show-payment-status-insufficient">
                                    Insufficient Amount
                                </span>
                            @else
                                <span class="orders-show-payment-status orders-show-payment-status-awaiting">
                                    Awaiting Payment
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            
                @if($order->expires_at)
                    <div class="orders-show-payment-expiry">
                        <p>The payment window expires in {{ $order->expires_at->diffForHumans() }}. Your order will be automatically canceled if the required amount of Monero for the purchase hasn't been met, and any incomplete amount will not be returned to your address after automatic cancellation.</p>
                    </div>

                    <div class="orders-show-payment-disclaimer">
                        <p>After completing the order, if you (the buyer) or the vendor cancels the order, a small cancellation fee will be applied to protect our website and prevent spam. This means you will receive slightly less than the original amount when your money is refunded.</p>
                    </div>
                @endif
            </div>
        
            @if(!$order->is_paid)
                <div class="orders-show-payment-card">
                    @if($qrCode)
                        <h2 class="orders-show-payment-subtitle">Scan QR Code</h2>
                        <div class="orders-show-payment-qr">
                            <img src="{{ $qrCode }}" alt="Payment QR Code" class="orders-show-payment-qr-image">
                        </div>
                    @endif
                
                    <h2 class="orders-show-payment-subtitle" style="margin-top: 20px;">Payment Address</h2>
                    <div class="orders-show-payment-address">
                        {{ $order->payment_address }}
                    </div>
                
                    <div class="orders-show-payment-refresh">
                        <a href="{{ route('orders.show', $order->unique_url) }}" class="orders-show-payment-refresh-btn">
                            Refresh to check for new transactions
                        </a>
                    </div>
                </div>
            @endif
        </div>
    @endif

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
                <div class="orders-show-info-item">
                    <div class="orders-show-info-label">Total Items</div>
                    <div class="orders-show-info-value">{{ $totalItems }}</div>
                </div>
                <div class="orders-show-info-item">
                    <div class="orders-show-info-label">XMR/USD Rate</div>
                    <div class="orders-show-info-value">${{ number_format($order->xmr_usd_rate, 2) }}</div>
                </div>
                <div class="orders-show-info-item">
                    <div class="orders-show-info-label">Monero Amount</div>
                    <div class="orders-show-info-value total">ɱ{{ number_format($order->required_xmr_amount, 12) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Cancel Button Section (new location) --}}
    @if($isBuyer && ($order->status === 'waiting_payment' || $order->status === 'payment_received' || $order->status === 'product_sent'))
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
                                
                                {{-- Display delivery text when order is sent or completed --}}
                                @if(($order->status === 'product_sent' || $order->status === 'completed') && $item->delivery_text)
                                    <div class="orders-show-item-delivery-text-container">
                                        <h4>Delivery Information:</h4>
                                        <div class="orders-show-item-delivery-text">
                                            {{ $item->delivery_text }}
                                        </div>
                                    </div>
                                @endif
                                
                                {{-- Display product type pill --}}
                                <div class="orders-show-item-type {{ $item->product ? ($item->product->type === 'digital' ? 'type-digital' : ($item->product->type === 'cargo' ? 'type-cargo' : 'type-deaddrop')) : 'type-deleted' }}">
                                    @if(!$item->product)
                                        Product Deleted
                                    @elseif($item->product->type === 'digital')
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
                                    {{ $item->product && $item->product->category ? $item->product->category->name : 'Uncategorized' }}
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

    {{-- Dispute Form Section --}}
    @if($isBuyer && $order->status === 'product_sent')
        <div class="orders-show-dispute-form-container">
            <div class="orders-show-dispute-form-card">
                <h2 class="orders-show-dispute-form-title">Do You Want to Open a Dispute?</h2>
                <form action="{{ route('disputes.store', $order->unique_url) }}" method="POST">
                    @csrf
                    <div class="orders-show-dispute-form-description">
                        Please explain why you are opening this dispute. Be specific and provide any relevant details.
                    </div>
                    <textarea name="reason" placeholder="Reason for dispute... (8-1600 characters)" required minlength="8" maxlength="1600" class="orders-show-dispute-form-textarea"></textarea>
                    <div class="orders-show-dispute-form-submit">
                        <button type="submit" class="orders-show-dispute-form-button">Submit Dispute</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

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
    
    {{-- Dispute Section --}}
    @if($dispute)
        <div class="orders-show-dispute-container">
            <div class="orders-show-dispute-card">
                <h2 class="orders-show-dispute-title">Dispute Information</h2>
                <div class="orders-show-dispute-status orders-show-dispute-status-{{ strtolower($dispute->status) }}">
                    Status: {{ $dispute->getFormattedStatus() }}
                </div>
            
                <div class="orders-show-dispute-info">
                    <h3 class="orders-show-dispute-section-title">Reason:</h3>
                    <div class="orders-show-dispute-text">{{ $dispute->reason }}</div>
                
                    @if($dispute->resolved_at)
                        <div class="orders-show-dispute-resolved-date">
                            Resolved on: {{ $dispute->resolved_at->format('Y-m-d / H:i') }}
                        </div>
                    @endif
                </div>
                
                <div class="orders-show-dispute-link-container">
                    <a href="{{ route('disputes.show', $dispute->id) }}" class="orders-show-dispute-link">
                        View Dispute Chat
                    </a>
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
                                        <textarea id="review_text_{{ $item->id }}" name="review_text" required minlength="8" maxlength="800" placeholder="Write your review here... (8-800 characters)" class="orders-show-review-textarea"></textarea>
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
