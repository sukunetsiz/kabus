@extends('layouts.app')

@section('content')
<div class="order-container">
    {{-- Breadcrumb Navigation --}}
    <div class="order-breadcrumb">
        <a href="{{ route('home') }}">Home</a>
        <span>/</span>
        <a href="{{ route('orders.index') }}">My Orders</a>
        <span>/</span>
        <span class="order-breadcrumb-current">Order Details</span>
    </div>

    <div class="order-header">
        <h1 class="order-title">Order Details</h1>
        <div class="order-id">ID: {{ substr($order->id, 0, 8) }}</div>
    </div>

    {{-- Order Status --}}
    <div class="order-status-container">
        <div class="order-status-card order-status-{{ $order->status }}">
            <h2 class="order-status-title">Status: {{ $order->getFormattedStatus() }}</h2>
            
            <div class="order-status-steps">
                <div class="order-status-step {{ $order->status === 'waiting_payment' || $order->is_paid || $order->is_delivered || $order->is_completed ? 'active' : '' }}">
                    <div class="order-status-step-number">1</div>
                    <div class="order-status-step-label">Waiting for Payment</div>
                    @if($order->paid_at)
                        <div class="order-status-step-date">{{ $order->paid_at->format('M d, Y') }}</div>
                    @endif
                </div>
                <div class="order-status-step {{ $order->is_paid || $order->is_delivered || $order->is_completed ? 'active' : '' }}">
                    <div class="order-status-step-number">2</div>
                    <div class="order-status-step-label">Payment Received</div>
                    @if($order->delivered_at)
                        <div class="order-status-step-date">{{ $order->delivered_at->format('M d, Y') }}</div>
                    @endif
                </div>
                <div class="order-status-step {{ $order->is_delivered || $order->is_completed ? 'active' : '' }}">
                    <div class="order-status-step-number">3</div>
                    <div class="order-status-step-label">Product Delivered</div>
                    @if($order->completed_at)
                        <div class="order-status-step-date">{{ $order->completed_at->format('M d, Y') }}</div>
                    @endif
                </div>
                <div class="order-status-step {{ $order->is_completed ? 'active' : '' }}">
                    <div class="order-status-step-number">4</div>
                    <div class="order-status-step-label">Order Completed</div>
                </div>
            </div>

            {{-- Status-based Actions --}}
            @if($isBuyer)
                @if($order->status === 'waiting_payment')
                    <div class="order-actions">
                        <form action="{{ route('orders.mark-paid', $order->unique_url) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-order-action btn-payment-done">Payment Done</button>
                        </form>
                    </div>
                @elseif($order->status === 'product_delivered')
                    <div class="order-actions">
                        <form action="{{ route('orders.mark-completed', $order->unique_url) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-order-action btn-confirm-delivery">Confirm Product as Delivered</button>
                        </form>
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- Order Details --}}
    <div class="order-details-container">
        <div class="order-details-card">
            <h2 class="order-details-title">Order Information</h2>
            
            <div class="order-info-grid">
                <div class="order-info-item">
                    <div class="order-info-label">Order Date</div>
                    <div class="order-info-value">{{ $order->created_at->format('M d, Y h:i A') }}</div>
                </div>
                <div class="order-info-item">
                    <div class="order-info-label">Vendor</div>
                    <div class="order-info-value">{{ $order->vendor->username }}</div>
                </div>
                <div class="order-info-item">
                    <div class="order-info-label">Subtotal</div>
                    <div class="order-info-value">${{ number_format($order->subtotal, 2) }}</div>
                </div>
                <div class="order-info-item">
                    <div class="order-info-label">Commission</div>
                    <div class="order-info-value">${{ number_format($order->commission, 2) }}</div>
                </div>
                <div class="order-info-item">
                    <div class="order-info-label">Total</div>
                    <div class="order-info-value total">${{ number_format($order->total, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Order Items --}}
    <div class="order-items-container">
        <div class="order-items-card">
            <h2 class="order-items-title">Items</h2>
            
            <div class="order-items-list">
                @foreach($order->items as $item)
                    <div class="order-item">
                        <div class="order-item-details">
                            <h3 class="order-item-name">{{ $item->product_name }}</h3>
                            <div class="order-item-description">{{ Str::limit($item->product_description, 200) }}</div>
                            
                            <div class="order-item-meta">
                                @if($item->bulk_option)
                                    <div class="order-item-quantity">
                                        {{ $item->quantity }} sets of {{ $item->bulk_option['amount'] ?? 0 }} 
                                        (Total: {{ $item->quantity * ($item->bulk_option['amount'] ?? 1) }})
                                    </div>
                                @else
                                    <div class="order-item-quantity">
                                        Quantity: {{ $item->quantity }}
                                    </div>
                                @endif
                                
                                @if($item->delivery_option)
                                    <div class="order-item-delivery">
                                        Delivery: {{ $item->delivery_option['description'] ?? 'N/A' }}
                                        ({{ isset($item->delivery_option['price']) ? '$' . number_format($item->delivery_option['price'], 2) : 'N/A' }})
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="order-item-price">
                            <div class="order-item-price-label">Price:</div>
                            <div class="order-item-price-value">${{ number_format($item->getTotalPrice(), 2) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Message Section --}}
    @if($order->encrypted_message)
        <div class="order-message-container">
            <div class="order-message-card">
                <h2 class="order-message-title">Encrypted Message</h2>
                <div class="order-message-content">
                    <textarea readonly class="order-message-textarea">{{ $order->encrypted_message }}</textarea>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    .order-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .order-breadcrumb {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        font-size: 14px;
    }
    
    .order-breadcrumb a {
        color: #666;
        text-decoration: none;
    }
    
    .order-breadcrumb span {
        margin: 0 10px;
        color: #999;
    }
    
    .order-breadcrumb-current {
        color: #333;
        font-weight: 600;
    }
    
    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .order-title {
        font-size: 24px;
        font-weight: 600;
        margin: 0;
    }
    
    .order-id {
        font-size: 16px;
        color: #666;
    }
    
    .order-status-container,
    .order-details-container,
    .order-items-container,
    .order-message-container {
        margin-bottom: 30px;
    }
    
    .order-status-card,
    .order-details-card,
    .order-items-card,
    .order-message-card {
        background-color: #3c3c3c;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }
    
    .order-status-waiting_payment {
        border-top: 4px solid #e53e3e;
    }
    
    .order-status-payment_received {
        border-top: 4px solid #dd6b20;
    }
    
    .order-status-product_delivered {
        border-top: 4px solid #38a169;
    }
    
    .order-status-completed {
        border-top: 4px solid #3182ce;
    }
    
    .order-status-title,
    .order-details-title,
    .order-items-title,
    .order-message-title {
        font-size: 18px;
        font-weight: 600;
        margin-top: 0;
        margin-bottom: 20px;
    }
    
    .order-status-steps {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
    }
    
    .order-status-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        flex: 1;
        position: relative;
        opacity: 0.5;
    }
    
    .order-status-step.active {
        opacity: 1;
    }
    
    .order-status-step::after {
        content: '';
        position: absolute;
        top: 20px;
        left: 50%;
        width: 100%;
        height: 2px;
        background-color: #cbd5e0;
        z-index: 1;
    }
    
    .order-status-step:last-child::after {
        display: none;
    }
    
    .order-status-step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #cbd5e0;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 16px;
        margin-bottom: 10px;
        position: relative;
        z-index: 2;
    }
    
    .order-status-step.active .order-status-step-number {
        background-color: #4a5568;
    }
    
    .order-status-step-label {
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 5px;
    }
    
    .order-status-step-date {
        font-size: 12px;
        color: #718096;
    }
    
    .order-actions {
        margin-top: 20px;
        text-align: center;
    }
    
    .btn-order-action {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .btn-payment-done {
        background-color: #48bb78;
        color: white;
    }
    
    .btn-confirm-delivery {
        background-color: #4299e1;
        color: white;
    }
    
    .order-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 15px;
    }
    
    .order-info-item {
        padding: 10px 0;
        border-bottom: 1px solid #edf2f7;
    }
    
    .order-info-label {
        font-size: 14px;
        color: #718096;
        margin-bottom: 5px;
    }
    
    .order-info-value {
        font-size: 16px;
        font-weight: 500;
    }
    
    .order-info-value.total {
        font-weight: 700;
        color: #2d3748;
    }
    
    .order-items-list {
        display: flex;
        flex-direction: column;
    }
    
    .order-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 20px 0;
        border-bottom: 1px solid #edf2f7;
    }
    
    .order-item:last-child {
        border-bottom: none;
    }
    
    .order-item-details {
        flex: 1;
    }
    
    .order-item-name {
        font-size: 16px;
        font-weight: 600;
        margin: 0 0 10px 0;
    }
    
    .order-item-description {
        font-size: 14px;
        color: #4a5568;
        margin-bottom: 15px;
    }
    
    .order-item-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .order-item-quantity,
    .order-item-delivery {
        font-size: 14px;
        color: #718096;
    }
    
    .order-item-price {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        margin-left: 20px;
    }
    
    .order-item-price-label {
        font-size: 14px;
        color: #718096;
        margin-bottom: 5px;
    }
    
    .order-item-price-value {
        font-size: 16px;
        font-weight: 600;
    }
    
    .order-message-content {
        width: 100%;
    }
    
    .order-message-textarea {
        width: 100%;
        min-height: 150px;
        padding: 10px;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        font-family: monospace;
        font-size: 12px;
        resize: vertical;
        background-color: #f7fafc;
    }
    
    @media (max-width: 768px) {
        .order-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .order-id {
            margin-top: 5px;
        }
        
        .order-status-steps {
            flex-direction: column;
            gap: 30px;
        }
        
        .order-status-step {
            flex-direction: row;
            justify-content: flex-start;
            gap: 15px;
            width: 100%;
        }
        
        .order-status-step::after {
            top: auto;
            left: 20px;
            width: 2px;
            height: 30px;
            transform: translateY(100%);
        }
        
        .order-item {
            flex-direction: column;
        }
        
        .order-item-price {
            flex-direction: row;
            justify-content: space-between;
            width: 100%;
            margin-left: 0;
            margin-top: 15px;
        }
    }
</style>
@endsection
