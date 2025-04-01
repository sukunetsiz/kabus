@extends('layouts.app')

@section('content')

<div class="orders-show-container">
    <div class="orders-show-header">
        <h1 class="orders-show-title">Sale Details</h1>
        <div class="orders-show-id">ID: {{ substr($sale->id, 0, 8) }}</div>
    </div>

    {{-- Order Status --}}
    <div class="orders-show-status-container">
        <div class="orders-show-status-card orders-show-status-{{ $sale->status }}">
            <h2 class="orders-show-status-title">Status: {{ $sale->getFormattedStatus() }}</h2>
            
            <div class="orders-show-status-steps {{ $sale->status === 'cancelled' ? 'with-cancelled' : '' }} {{ isset($sale->dispute) && $sale->dispute ? 'with-disputed' : '' }}">
                <div class="orders-show-status-step {{ $sale->status === 'waiting_payment' || $sale->is_paid || $sale->is_sent || $sale->is_completed ? 'active' : '' }} {{ $sale->status === 'cancelled' && !$sale->paid_at ? 'cancelled-step' : '' }}">
                    <div class="orders-show-status-step-number">1</div>
                    <div class="orders-show-status-step-label">Waiting for Payment</div>
                    @if($sale->created_at)
                        <div class="orders-show-status-step-date">{{ $sale->created_at->format('Y-m-d / H:i') }}</div>
                    @endif
                    @if($sale->status === 'cancelled' && !$sale->paid_at)
                        <div class="orders-show-status-cancelled-marker">
                            <div class="orders-show-status-cancelled-x">X</div>
                        </div>
                    @endif
                </div>
                <div class="orders-show-status-step {{ $sale->is_paid || $sale->is_sent || $sale->is_completed ? 'active' : '' }} {{ $sale->status === 'cancelled' && $sale->paid_at && !$sale->sent_at ? 'cancelled-step' : '' }}">
                    <div class="orders-show-status-step-number">2</div>
                    <div class="orders-show-status-step-label">Payment Received</div>
                    @if($sale->paid_at)
                        <div class="orders-show-status-step-date">{{ $sale->paid_at->format('Y-m-d / H:i') }}</div>
                    @endif
                    @if($sale->status === 'cancelled' && $sale->paid_at && !$sale->sent_at)
                        <div class="orders-show-status-cancelled-marker">
                            <div class="orders-show-status-cancelled-x">X</div>
                        </div>
                    @endif
                </div>
                <div class="orders-show-status-step {{ $sale->is_sent || $sale->is_completed ? 'active' : '' }} {{ $sale->status === 'cancelled' && $sale->sent_at && !$sale->completed_at ? 'cancelled-step' : '' }}">
                    <div class="orders-show-status-step-number">3</div>
                    <div class="orders-show-status-step-label">Product Sent</div>
                    @if($sale->sent_at)
                        <div class="orders-show-status-step-date">{{ $sale->sent_at->format('Y-m-d / H:i') }}</div>
                    @endif
                    @if($sale->status === 'cancelled' && $sale->sent_at && !$sale->completed_at)
                        <div class="orders-show-status-cancelled-marker">
                            <div class="orders-show-status-cancelled-x">X</div>
                        </div>
                    @endif
                    @if(isset($sale->dispute) && $sale->dispute)
                        <div class="orders-show-status-disputed-marker">
                            <div class="orders-show-status-disputed-question">?</div>
                        </div>
                    @endif
                </div>
                <div class="orders-show-status-step {{ $sale->is_completed ? 'active' : '' }}">
                    <div class="orders-show-status-step-number">4</div>
                    <div class="orders-show-status-step-label">Order Completed</div>
                    @if($sale->completed_at)
                        <div class="orders-show-status-step-date">{{ $sale->completed_at->format('Y-m-d / H:i') }}</div>
                    @endif
                </div>
            </div>

            {{-- Status-based Actions for Vendor --}}
            @if($sale->status === 'payment_received')
                {{-- Auto-cancel notice --}}
                <div class="orders-show-status-explanation">
                    <p><strong>Important Notice:</strong> This order will be automatically cancelled if not marked as sent within 
                    <strong>96 hours (4 days)</strong> after payment was received.</p>
                    
                    @if($sale->getAutoCancelDeadline())
                        <p>Auto-cancel deadline: <strong>{{ $sale->getAutoCancelDeadline()->format('Y-m-d H:i') }}</strong> 
                        ({{ $sale->getAutoCancelDeadline()->diffForHumans() }})</p>
                    @endif
                </div>
                
                <div class="orders-show-actions">
                    {{-- Delivery text input form --}}
                    <form action="{{ route('vendor.sales.update-delivery-text', $sale->unique_url) }}" method="POST" class="sales-show-delivery-form">
                        @csrf
                        <h3 class="sales-show-delivery-title">Delivery Information</h3>
                        <p class="sales-show-delivery-desc">Please enter delivery information for each product below before marking the order as sent</p>

                        @foreach($sale->items as $item)
                            @if($item->product)
                                <div class="sales-show-delivery-item">
                                    <label for="delivery_text_{{ $item->product_id }}" class="sales-show-delivery-label">
                                        <strong>{{ $item->product_name }}</strong>
                                        <span class="sales-show-delivery-instruction">Enter delivery details (e.g., GPS coordinates, a website link, or cargo tracking number)</span>
                                    </label>
                                    <textarea 
                                        id="delivery_text_{{ $item->product_id }}" 
                                        name="delivery_text[{{ $item->product_id }}]" 
                                        rows="3" 
                                        class="sales-show-delivery-textarea"
                                        required>{{ $item->delivery_text }}</textarea>
                                </div>
                            @endif
                        @endforeach
                        
                        <button type="submit" class="orders-show-action-btn sales-show-update-btn">Update Delivery Information</button>
                    </form>

                    <form action="{{ route('orders.mark-sent', $sale->unique_url) }}" method="POST" class="sales-show-deliver-form">
                        @csrf
                        <button type="submit" class="orders-show-action-btn sales-show-deliver-btn">Deliver Products</button>
                    </form>
                </div>
            @endif
        </div>
            {{-- Cancel Button (only show for non-completed, non-cancelled orders, and when no dispute is active) --}}
            @if($sale->status !== 'completed' && $sale->status !== 'cancelled' && !$sale->dispute)
                <div class="orders-show-actions">
                    <form action="{{ route('orders.mark-cancelled', $sale->unique_url) }}" method="POST">
                        @csrf
                        <button type="submit" class="orders-show-action-btn orders-show-cancel-btn">Cancel Sale</button>
                    </form>
                </div>
            @endif
    </div>

    
    {{-- Dispute Section --}}
    @if($sale->dispute)
        <div class="orders-show-dispute-container">
            <div class="orders-show-dispute-card">
                <h2 class="orders-show-dispute-title">Dispute Information</h2>
                <div class="orders-show-dispute-status orders-show-dispute-status-{{ strtolower($sale->dispute->status) }}">
                    Status: {{ $sale->dispute->getFormattedStatus() }}
                </div>
            
                <div class="orders-show-dispute-info">
                    <h3 class="orders-show-dispute-section-title">Reason:</h3>
                    <div class="orders-show-dispute-text">{{ $sale->dispute->reason }}</div>
                
                    @if($sale->dispute->resolved_at)
                        <div class="orders-show-dispute-resolved-date">
                            Resolved on: {{ $sale->dispute->resolved_at->format('Y-m-d / H:i') }}
                        </div>
                    @endif
                </div>
                
                <div class="orders-show-dispute-link-container">
                    <a href="{{ route('vendor.disputes.show', $sale->dispute->id) }}" class="orders-show-dispute-link">
                        View Dispute Chat
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Sale Details --}}
    <div class="orders-show-details-container">
        <div class="orders-show-details-card">
            <h2 class="orders-show-details-title">Sale Information</h2>
            
            <div class="orders-show-info-grid">
                <div class="orders-show-info-item">
                    <div class="orders-show-info-label">Order Date</div>
                    <div class="orders-show-info-value">{{ $sale->created_at->format('Y-m-d / H:i') }}</div>
                </div>
                <div class="orders-show-info-item">
                    <div class="orders-show-info-label">Buyer</div>
                    <div class="orders-show-info-value">{{ $sale->user->username }}</div>
                </div>
                <div class="orders-show-info-item">
                    <div class="orders-show-info-label">Subtotal</div>
                    <div class="orders-show-info-value">${{ number_format($sale->subtotal, 2) }}</div>
                </div>
                <div class="orders-show-info-item">
                    <div class="orders-show-info-label">Commission</div>
                    <div class="orders-show-info-value">${{ number_format($sale->commission, 2) }}</div>
                </div>
                <div class="orders-show-info-item">
                    <div class="orders-show-info-label">Total</div>
                    <div class="orders-show-info-value total">${{ number_format($sale->total, 2) }}</div>
                </div>
                <div class="orders-show-info-item">
                    <div class="orders-show-info-label">Total Items</div>
                    <div class="orders-show-info-value">{{ $totalItems }}</div>
                </div>
                <div class="orders-show-info-item">
                    <div class="orders-show-info-label">XMR/USD Rate</div>
                    <div class="orders-show-info-value">${{ number_format($sale->xmr_usd_rate, 2) }}</div>
                </div>
                <div class="orders-show-info-item">
                    <div class="orders-show-info-label">Monero Amount</div>
                    <div class="orders-show-info-value total">ɱ{{ number_format($sale->required_xmr_amount, 12) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sale Items --}}
    <div class="orders-show-items-container">
        <div class="orders-show-items-card">
            <h2 class="orders-show-items-title">Items</h2>
            
            <div class="orders-show-items-list">
                @foreach($sale->items as $item)
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
                                @if(($sale->status === 'product_sent' || $sale->status === 'completed') && $item->delivery_text)
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
    @if($sale->encrypted_message)
        <div class="orders-show-message-container">
            <div class="orders-show-message-card">
                <h2 class="orders-show-message-title">Encrypted Message from Buyer</h2>
                <div class="orders-show-message-content">
                    <textarea readonly class="orders-show-message-textarea">{{ $sale->encrypted_message }}</textarea>
                </div>
            </div>
        </div>
    @endif
</div>

@endsection
