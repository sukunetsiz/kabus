@extends('layouts.app')

@section('content')
<div class="sales-show-container">
    <div class="sales-show-header">
        <h1 class="sales-show-title">Sale Details</h1>
        <div class="sales-show-id">ID: {{ substr($sale->id, 0, 8) }}</div>
    </div>

    {{-- Order Status --}}
    <div class="sales-show-status-container">
        <div class="sales-show-status-card sales-show-status-{{ $sale->status }}">
            <h2 class="sales-show-status-title">Status: {{ $sale->getFormattedStatus() }}</h2>
            
            <div class="sales-show-status-steps {{ $sale->status === 'cancelled' ? 'with-cancelled' : '' }}">
                <div class="sales-show-status-step {{ $sale->status === 'waiting_payment' || $sale->is_paid || $sale->is_delivered || $sale->is_completed ? 'active' : '' }} {{ $sale->status === 'cancelled' && !$sale->paid_at ? 'cancelled-step' : '' }}">
                    <div class="sales-show-status-step-number">1</div>
                    <div class="sales-show-status-step-label">Waiting for Payment</div>
                    @if($sale->paid_at)
                        <div class="sales-show-status-step-date">{{ $sale->paid_at->format('Y-m-d / H:i') }}</div>
                    @endif
                    @if($sale->status === 'cancelled' && !$sale->paid_at)
                        <div class="sales-show-status-cancelled-marker">
                            <div class="sales-show-status-cancelled-x">X</div>
                        </div>
                    @endif
                </div>
                <div class="sales-show-status-step {{ $sale->is_paid || $sale->is_delivered || $sale->is_completed ? 'active' : '' }} {{ $sale->status === 'cancelled' && $sale->paid_at && !$sale->delivered_at ? 'cancelled-step' : '' }}">
                    <div class="sales-show-status-step-number">2</div>
                    <div class="sales-show-status-step-label">Payment Received</div>
                    @if($sale->delivered_at)
                        <div class="sales-show-status-step-date">{{ $sale->delivered_at->format('Y-m-d / H:i') }}</div>
                    @endif
                    @if($sale->status === 'cancelled' && $sale->paid_at && !$sale->delivered_at)
                        <div class="sales-show-status-cancelled-marker">
                            <div class="sales-show-status-cancelled-x">X</div>
                        </div>
                    @endif
                </div>
                <div class="sales-show-status-step {{ $sale->is_delivered || $sale->is_completed ? 'active' : '' }} {{ $sale->status === 'cancelled' && $sale->delivered_at && !$sale->completed_at ? 'cancelled-step' : '' }}">
                    <div class="sales-show-status-step-number">3</div>
                    <div class="sales-show-status-step-label">Product Delivered</div>
                    @if($sale->completed_at)
                        <div class="sales-show-status-step-date">{{ $sale->completed_at->format('Y-m-d / H:i') }}</div>
                    @endif
                    @if($sale->status === 'cancelled' && $sale->delivered_at && !$sale->completed_at)
                        <div class="sales-show-status-cancelled-marker">
                            <div class="sales-show-status-cancelled-x">X</div>
                        </div>
                    @endif
                </div>
                <div class="sales-show-status-step {{ $sale->is_completed ? 'active' : '' }}">
                    <div class="sales-show-status-step-number">4</div>
                    <div class="sales-show-status-step-label">Order Completed</div>
                </div>
            </div>

            {{-- Status-based Actions for Vendor --}}
            @if($sale->status === 'payment_received')
                <div class="sales-show-actions">
                    {{-- Delivery text input form --}}
                    <form action="{{ route('vendor.sales.update-delivery-text', $sale->unique_url) }}" method="POST" class="sales-show-delivery-form">
                        @csrf
                        <h3 class="sales-show-delivery-title">Delivery Information</h3>
                        <p class="sales-show-delivery-desc">Please enter delivery information for each product below before marking the order as delivered</p>

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
                        
                        <button type="submit" class="sales-show-action-btn sales-show-update-btn">Update Delivery Information</button>
                    </form>

                    <form action="{{ route('orders.mark-delivered', $sale->unique_url) }}" method="POST" class="sales-show-deliver-form">
                        @csrf
                        <button type="submit" class="sales-show-action-btn sales-show-deliver-btn">Deliver Products</button>
                    </form>
                </div>
            @endif
        </div>
            {{-- Cancel Button (only show for non-completed and non-cancelled orders) --}}
            @if($sale->status !== 'completed' && $sale->status !== 'cancelled')
                <div class="sales-show-actions">
                    <form action="{{ route('orders.mark-cancelled', $sale->unique_url) }}" method="POST">
                        @csrf
                        <button type="submit" class="sales-show-action-btn sales-show-cancel-btn">Cancel Sale</button>
                    </form>
                </div>
            @endif
    </div>

    {{-- Buyer Information --}}
    <div class="sales-show-details-container">
        <div class="sales-show-details-card">
            <h2 class="sales-show-details-title">Buyer Information</h2>
            
            <div class="sales-show-info-grid">
                <div class="sales-show-info-item">
                    <div class="sales-show-info-label">Username</div>
                    <div class="sales-show-info-value">{{ $sale->user->username }}</div>
                </div>
                <div class="sales-show-info-item">
                    <div class="sales-show-info-label">Order Date</div>
                    <div class="sales-show-info-value">{{ $sale->created_at->format('Y-m-d / H:i') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sale Details --}}
    <div class="sales-show-details-container">
        <div class="sales-show-details-card">
            <h2 class="sales-show-details-title">Sale Information</h2>
            
            <div class="sales-show-info-grid">
                <div class="sales-show-info-item">
                    <div class="sales-show-info-label">Subtotal</div>
                    <div class="sales-show-info-value">${{ number_format($sale->subtotal, 2) }}</div>
                </div>
                <div class="sales-show-info-item">
                    <div class="sales-show-info-label">Commission</div>
                    <div class="sales-show-info-value">${{ number_format($sale->commission, 2) }}</div>
                </div>
                <div class="sales-show-info-item">
                    <div class="sales-show-info-label">Total</div>
                    <div class="sales-show-info-value total">${{ number_format($sale->total, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sale Items --}}
    <div class="sales-show-items-container">
        <div class="sales-show-items-card">
            <h2 class="sales-show-items-title">Items</h2>
            
            <div class="sales-show-items-list">
                @foreach($sale->items as $item)
                    <div class="sales-show-item">
                        <div class="sales-show-item-details">
                            <h3 class="sales-show-item-name">{{ $item->product_name }}</h3>
                            <div class="sales-show-item-description">{{ Str::limit($item->product_description, 200) }}</div>
                            
                            <div class="sales-show-item-meta">
                                @if($item->bulk_option)
                                    <div class="sales-show-item-quantity">
                                        {{ $item->quantity }} sets of {{ $item->bulk_option['amount'] ?? 0 }} 
                                        (Total: {{ $item->quantity * ($item->bulk_option['amount'] ?? 1) }})
                                    </div>
                                @else
                                    <div class="sales-show-item-quantity">
                                        Quantity: {{ $item->quantity }}
                                    </div>
                                @endif
                                
                                @if($item->delivery_option)
                                    <div class="sales-show-item-delivery">
                                        Delivery: {{ $item->delivery_option['description'] ?? 'N/A' }}
                                        ({{ isset($item->delivery_option['price']) ? '$' . number_format($item->delivery_option['price'], 2) : 'N/A' }})
                                    </div>
                                @endif
                                
                                {{-- Display delivery text when order is delivered or completed --}}
                                @if(($sale->status === 'product_delivered' || $sale->status === 'completed') && $item->delivery_text)
                                    <div class="sales-show-item-delivery-text-container">
                                        <h4>Delivery Information:</h4>
                                        <div class="sales-show-item-delivery-text">
                                            {{ $item->delivery_text }}
                                        </div>
                                    </div>
                                @endif
                                
                                {{-- Display product type pill --}}
                                <div class="sales-show-item-type {{ $item->product->type === 'digital' ? 'type-digital' : ($item->product->type === 'cargo' ? 'type-cargo' : 'type-deaddrop') }}">
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
                                <div class="sales-show-item-category">
                                    {{ $item->product->category->name }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="sales-show-item-price">
                            <div class="sales-show-item-price-label">Price:</div>
                            <div class="sales-show-item-price-value">${{ number_format($item->getTotalPrice(), 2) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Message Section --}}
    @if($sale->encrypted_message)
        <div class="sales-show-message-container">
            <div class="sales-show-message-card">
                <h2 class="sales-show-message-title">Encrypted Message from Buyer</h2>
                <div class="sales-show-message-content">
                    <textarea readonly class="sales-show-message-textarea">{{ $sale->encrypted_message }}</textarea>
                </div>
            </div>
        </div>
    @endif
</div>

@endsection
