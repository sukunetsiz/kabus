@extends('layouts.app')

@section('content')
<div class="sale-container">
    {{-- Breadcrumb Navigation --}}
    <div class="sale-breadcrumb">
        <a href="{{ route('vendor.index') }}">Vendor Dashboard</a>
        <span>/</span>
        <a href="{{ route('vendor.sales') }}">Sales</a>
        <span>/</span>
        <span class="sale-breadcrumb-current">Sale Details</span>
    </div>

    <div class="sale-header">
        <h1 class="sale-title">Sale Details</h1>
        <div class="sale-id">ID: {{ substr($sale->id, 0, 8) }}</div>
    </div>

    {{-- Order Status --}}
    <div class="sale-status-container">
        <div class="sale-status-card sale-status-{{ $sale->status }}">
            <h2 class="sale-status-title">Status: {{ $sale->getFormattedStatus() }}</h2>
            
            <div class="sale-status-steps">
                <div class="sale-status-step {{ $sale->status === 'waiting_payment' || $sale->is_paid || $sale->is_delivered || $sale->is_completed ? 'active' : '' }}">
                    <div class="sale-status-step-number">1</div>
                    <div class="sale-status-step-label">Waiting for Payment</div>
                    @if($sale->paid_at)
                        <div class="sale-status-step-date">{{ $sale->paid_at->format('M d, Y') }}</div>
                    @endif
                </div>
                <div class="sale-status-step {{ $sale->is_paid || $sale->is_delivered || $sale->is_completed ? 'active' : '' }}">
                    <div class="sale-status-step-number">2</div>
                    <div class="sale-status-step-label">Payment Received</div>
                    @if($sale->delivered_at)
                        <div class="sale-status-step-date">{{ $sale->delivered_at->format('M d, Y') }}</div>
                    @endif
                </div>
                <div class="sale-status-step {{ $sale->is_delivered || $sale->is_completed ? 'active' : '' }}">
                    <div class="sale-status-step-number">3</div>
                    <div class="sale-status-step-label">Product Delivered</div>
                    @if($sale->completed_at)
                        <div class="sale-status-step-date">{{ $sale->completed_at->format('M d, Y') }}</div>
                    @endif
                </div>
                <div class="sale-status-step {{ $sale->is_completed ? 'active' : '' }}">
                    <div class="sale-status-step-number">4</div>
                    <div class="sale-status-step-label">Order Completed</div>
                </div>
            </div>

            {{-- Status-based Actions for Vendor --}}
            @if($sale->status === 'payment_received')
                <div class="sale-actions">
                    <form action="{{ route('orders.mark-delivered', $sale->unique_url) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-sale-action btn-product-sent">Product Sent</button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    {{-- Buyer Information --}}
    <div class="sale-buyer-container">
        <div class="sale-buyer-card">
            <h2 class="sale-buyer-title">Buyer Information</h2>
            
            <div class="sale-buyer-info">
                <div class="sale-buyer-item">
                    <div class="sale-buyer-label">Username</div>
                    <div class="sale-buyer-value">{{ $sale->user->username }}</div>
                </div>
                <div class="sale-buyer-item">
                    <div class="sale-buyer-label">Order Date</div>
                    <div class="sale-buyer-value">{{ $sale->created_at->format('M d, Y h:i A') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Order Details --}}
    <div class="sale-details-container">
        <div class="sale-details-card">
            <h2 class="sale-details-title">Sale Information</h2>
            
            <div class="sale-info-grid">
                <div class="sale-info-item">
                    <div class="sale-info-label">Subtotal</div>
                    <div class="sale-info-value">${{ number_format($sale->subtotal, 2) }}</div>
                </div>
                <div class="sale-info-item">
                    <div class="sale-info-label">Commission</div>
                    <div class="sale-info-value">${{ number_format($sale->commission, 2) }}</div>
                </div>
                <div class="sale-info-item">
                    <div class="sale-info-label">Total</div>
                    <div class="sale-info-value total">${{ number_format($sale->total, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sale Items --}}
    <div class="sale-items-container">
        <div class="sale-items-card">
            <h2 class="sale-items-title">Items</h2>
            
            <div class="sale-items-list">
                @foreach($sale->items as $item)
                    <div class="sale-item">
                        <div class="sale-item-details">
                            <h3 class="sale-item-name">{{ $item->product_name }}</h3>
                            <div class="sale-item-description">{{ Str::limit($item->product_description, 200) }}</div>
                            
                            <div class="sale-item-meta">
                                @if($item->bulk_option)
                                    <div class="sale-item-quantity">
                                        {{ $item->quantity }} sets of {{ $item->bulk_option['amount'] ?? 0 }} 
                                        (Total: {{ $item->quantity * ($item->bulk_option['amount'] ?? 1) }})
                                    </div>
                                @else
                                    <div class="sale-item-quantity">
                                        Quantity: {{ $item->quantity }}
                                    </div>
                                @endif
                                
                                @if($item->delivery_option)
                                    <div class="sale-item-delivery">
                                        Delivery: {{ $item->delivery_option['description'] ?? 'N/A' }}
                                        ({{ isset($item->delivery_option['price']) ? '$' . number_format($item->delivery_option['price'], 2) : 'N/A' }})
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="sale-item-price">
                            <div class="sale-item-price-label">Price:</div>
                            <div class="sale-item-price-value">${{ number_format($item->getTotalPrice(), 2) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Message Section --}}
    @if($sale->encrypted_message)
        <div class="sale-message-container">
            <div class="sale-message-card">
                <h2 class="sale-message-title">Encrypted Message from Buyer</h2>
                <div class="sale-message-content">
                    <textarea readonly class="sale-message-textarea">{{ $sale->encrypted_message }}</textarea>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    .sale-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .sale-breadcrumb {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        font-size: 14px;
    }
    
    .sale-breadcrumb a {
        color: #666;
        text-decoration: none;
    }
    
    .sale-breadcrumb span {
        margin: 0 10px;
        color: #999;
    }
    
    .sale-breadcrumb-current {
        color: #333;
        font-weight: 600;
    }
    
    .sale-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .sale-title {
        font-size: 24px;
        font-weight: 600;
        margin: 0;
    }
    
    .sale-id {
        font-size: 16px;
        color: #666;
    }
    
    .sale-status-container,
    .sale-buyer-container,
    .sale-details-container,
    .sale-items-container,
    .sale-message-container {
        margin-bottom: 30px;
    }
    
    .sale-status-card,
    .sale-buyer-card,
    .sale-details-card,
    .sale-items-card,
    .sale-message-card {
        background-color: #3c3c3c;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }
    
    .sale-status-waiting_payment {
        border-top: 4px solid #e53e3e;
    }
    
    .sale-status-payment_received {
        border-top: 4px solid #dd6b20;
    }
    
    .sale-status-product_delivered {
        border-top: 4px solid #38a169;
    }
    
    .sale-status-completed {
        border-top: 4px solid #3182ce;
    }
    
    .sale-status-title,
    .sale-buyer-title,
    .sale-details-title,
    .sale-items-title,
    .sale-message-title {
        font-size: 18px;
        font-weight: 600;
        margin-top: 0;
        margin-bottom: 20px;
    }
    
    .sale-status-steps {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
    }
    
    .sale-status-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        flex: 1;
        position: relative;
        opacity: 0.5;
    }
    
    .sale-status-step.active {
        opacity: 1;
    }
    
    .sale-status-step::after {
        content: '';
        position: absolute;
        top: 20px;
        left: 50%;
        width: 100%;
        height: 2px;
        background-color: #cbd5e0;
        z-index: 1;
    }
    
    .sale-status-step:last-child::after {
        display: none;
    }
    
    .sale-status-step-number {
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
    
    .sale-status-step.active .sale-status-step-number {
        background-color: #4a5568;
    }
    
    .sale-status-step-label {
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 5px;
    }
    
    .sale-status-step-date {
        font-size: 12px;
        color: #718096;
    }
    
    .sale-actions {
        margin-top: 20px;
        text-align: center;
    }
    
    .btn-sale-action {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .btn-product-sent {
        background-color: #38a169;
        color: white;
    }
    
    .sale-buyer-info {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 15px;
    }
    
    .sale-buyer-item {
        padding: 10px 0;
        border-bottom: 1px solid #edf2f7;
    }
    
    .sale-buyer-label {
        font-size: 14px;
        color: #718096;
        margin-bottom: 5px;
    }
    
    .sale-buyer-value {
        font-size: 16px;
        font-weight: 500;
    }
    
    .sale-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 15px;
    }
    
    .sale-info-item {
        padding: 10px 0;
        border-bottom: 1px solid #edf2f7;
    }
    
    .sale-info-label {
        font-size: 14px;
        color: #718096;
        margin-bottom: 5px;
    }
    
    .sale-info-value {
        font-size: 16px;
        font-weight: 500;
    }
    
    .sale-info-value.total {
        font-weight: 700;
        color: #2d3748;
    }
    
    .sale-items-list {
        display: flex;
        flex-direction: column;
    }
    
    .sale-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 20px 0;
        border-bottom: 1px solid #edf2f7;
    }
    
    .sale-item:last-child {
        border-bottom: none;
    }
    
    .sale-item-details {
        flex: 1;
    }
    
    .sale-item-name {
        font-size: 16px;
        font-weight: 600;
        margin: 0 0 10px 0;
    }
    
    .sale-item-description {
        font-size: 14px;
        color: #4a5568;
        margin-bottom: 15px;
    }
    
    .sale-item-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .sale-item-quantity,
    .sale-item-delivery {
        font-size: 14px;
        color: #718096;
    }
    
    .sale-item-price {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        margin-left: 20px;
    }
    
    .sale-item-price-label {
        font-size: 14px;
        color: #718096;
        margin-bottom: 5px;
    }
    
    .sale-item-price-value {
        font-size: 16px;
        font-weight: 600;
    }
    
    .sale-message-content {
        width: 100%;
    }
    
    .sale-message-textarea {
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
        .sale-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .sale-id {
            margin-top: 5px;
        }
        
        .sale-status-steps {
            flex-direction: column;
            gap: 30px;
        }
        
        .sale-status-step {
            flex-direction: row;
            justify-content: flex-start;
            gap: 15px;
            width: 100%;
        }
        
        .sale-status-step::after {
            top: auto;
            left: 20px;
            width: 2px;
            height: 30px;
            transform: translateY(100%);
        }
        
        .sale-item {
            flex-direction: column;
        }
        
        .sale-item-price {
            flex-direction: row;
            justify-content: space-between;
            width: 100%;
            margin-left: 0;
            margin-top: 15px;
        }
    }
</style>
@endsection
