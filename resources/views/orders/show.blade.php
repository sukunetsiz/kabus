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
            
            <div class="orders-show-status-steps">
                <div class="orders-show-status-step {{ $order->status === 'waiting_payment' || $order->is_paid || $order->is_delivered || $order->is_completed ? 'active' : '' }}">
                    <div class="orders-show-status-step-number">1</div>
                    <div class="orders-show-status-step-label">Waiting for Payment</div>
                    @if($order->paid_at)
                        <div class="orders-show-status-step-date">{{ $order->paid_at->format('M d, Y') }}</div>
                    @endif
                </div>
                <div class="orders-show-status-step {{ $order->is_paid || $order->is_delivered || $order->is_completed ? 'active' : '' }}">
                    <div class="orders-show-status-step-number">2</div>
                    <div class="orders-show-status-step-label">Payment Received</div>
                    @if($order->delivered_at)
                        <div class="orders-show-status-step-date">{{ $order->delivered_at->format('M d, Y') }}</div>
                    @endif
                </div>
                <div class="orders-show-status-step {{ $order->is_delivered || $order->is_completed ? 'active' : '' }}">
                    <div class="orders-show-status-step-number">3</div>
                    <div class="orders-show-status-step-label">Product Delivered</div>
                    @if($order->completed_at)
                        <div class="orders-show-status-step-date">{{ $order->completed_at->format('M d, Y') }}</div>
                    @endif
                </div>
                <div class="orders-show-status-step {{ $order->is_completed ? 'active' : '' }}">
                    <div class="orders-show-status-step-number">4</div>
                    <div class="orders-show-status-step-label">Order Completed</div>
                </div>
            </div>

            {{-- Status-based Actions --}}
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
                    <div class="orders-show-info-value">{{ $order->created_at->format('M d, Y h:i A') }}</div>
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
</div>

<style>
.orders-show-container {
max-width:1200px;
margin:0 auto;
padding:20px
}

.orders-show-header {
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:30px;
padding-bottom:15px;
border-bottom:2px solid #bb86fc
}

.orders-show-title {
font-size:28px;
font-weight:700;
margin:0;
margin-top:-20px;
color:#121212;
background-color:#bb86fc;
padding:10px 25px;
border-radius:30px;
display:inline-block;
box-shadow:0 4px 15px #bb86fc4d
}

.orders-show-id {
padding:6px 12px;
border-radius:20px;
font-size:16px;
font-weight:700;
color:#121212;
background-color:#bb86fc
}

.orders-show-status-container,.orders-show-details-container,.orders-show-items-container,.orders-show-message-container {
margin-bottom:30px
}

.orders-show-status-card,.orders-show-details-card,.orders-show-items-card {
background-color:#1e1e1e;
border-radius:12px;
box-shadow:0 6px 25px #0000004d;
padding:25px;
border:1px solid #3c3c3c;
transition:all .3s ease
}

.orders-show-message-card {
background-color:#1e1e1e;
border-radius:12px;
box-shadow:0 6px 25px #0000004d;
padding:25px;
border:1px solid #3c3c3c;
transition:all .3s ease;
width:60%;
margin:0 auto
}

.orders-show-status-waiting_payment {
border-left:4px solid #ff3838;
border-right:4px solid #ff3838
}

.orders-show-status-payment_received {
border-left:4px solid #ffc107;
border-right:4px solid #ffc107
}

.orders-show-status-product_delivered {
border-left:4px solid #4caf50;
border-right:4px solid #4caf50
}

.orders-show-status-completed {
border-left:4px solid #bb86fc;
border-right:4px solid #bb86fc
}

.orders-show-status-title,.orders-show-details-title,.orders-show-items-title,.orders-show-message-title {
font-size:24px;
font-weight:700;
margin-top:0;
margin-bottom:25px;
color:#bb86fc;
text-align:center
}

.orders-show-status-steps {
display:flex;
justify-content:space-between;
margin-bottom:30px;
position:relative
}

.orders-show-status-step {
display:flex;
flex-direction:column;
align-items:center;
text-align:center;
flex:1;
position:relative;
z-index:2
}

.orders-show-status-step:not(:last-child)::after {
content:'';
position:absolute;
top:25px;
left:50%;
width:100%;
height:3px;
background-color:#3c3c3c;
z-index:0
}

.orders-show-status-step.active:not(:last-child) + .orders-show-status-step.active::after,.orders-show-status-step.active:not(:last-child)::after {
background-color:#bb86fc
}

.orders-show-status-step.active {
opacity:1
}

.orders-show-status-step-number {
width:50px;
height:50px;
border-radius:50%;
background-color:#2c2c2c;
color:#e0e0e0;
display:flex;
align-items:center;
justify-content:center;
font-weight:700;
font-size:18px;
margin-bottom:12px;
border:2px solid #3c3c3c;
transition:all .3s ease;
position:relative;
z-index:2;
box-shadow:0 0 0 5px #1e1e1e
}

.orders-show-status-step.active .orders-show-status-step-number {
background-color:#bb86fc;
color:#121212;
border-color:#bb86fc;
transform:scale(1.1);
box-shadow:0 0 15px #bb86fc66
}

.orders-show-status-step-label {
font-size:16px;
font-weight:600;
margin-bottom:6px;
color:#e0e0e0
}

.orders-show-status-step-date {
font-size:14px;
color:#a0a0a0
}

.orders-show-actions {
margin-top:30px;
text-align:center
}

.orders-show-action-btn {
padding:12px 25px;
border:none;
border-radius:8px;
font-weight:700;
font-size:16px;
cursor:pointer;
transition:all .3s ease;
text-transform:uppercase;
letter-spacing:1px
}

.orders-show-action-btn:hover {
transform:translateY(-2px);
box-shadow:0 4px 15px #0000004d
}

.orders-show-payment-done-btn {
background-color:#4caf50;
color:#121212
}

.orders-show-payment-done-btn:hover {
background-color:#45a049
}

.orders-show-confirm-delivery-btn {
background-color:#bb86fc;
color:#121212
}

.orders-show-confirm-delivery-btn:hover {
background-color:#96c
}

.orders-show-info-grid {
display:grid;
grid-template-columns:repeat(auto-fill,minmax(250px,1fr));
gap:20px
}

.orders-show-info-item {
padding:15px;
border-radius:8px;
background-color:#2c2c2c;
transition:all .3s ease
}

.orders-show-info-item:hover {
transform:translateY(-3px);
box-shadow:0 4px 12px #bb86fc33
}

.orders-show-info-label {
font-size:14px;
color:#a0a0a0;
margin-bottom:8px;
text-align:center
}

.orders-show-info-value {
font-size:16px;
font-weight:600;
color:#e0e0e0;
text-align:center
}

.orders-show-info-value.total {
font-weight:700;
color:#bb86fc;
font-size:18px
}

.orders-show-info-grid {
display:grid;
grid-template-columns:repeat(auto-fill,minmax(250px,1fr));
gap:20px;
position:relative
}

.orders-show-info-item:last-child {
grid-column:1 / -1;
max-width:250px;
margin:0 auto
}

.orders-show-items-list {
display:flex;
flex-direction:column;
gap:20px
}

.orders-show-item {
display:flex;
justify-content:space-between;
align-items:flex-start;
padding:20px;
border-radius:8px;
background-color:#2c2c2c;
transition:all .3s ease
}

.orders-show-item:hover {
transform:translateY(-3px);
box-shadow:0 4px 12px #bb86fc33
}

.orders-show-item-details {
flex:1
}

.orders-show-item-name {
font-size:18px;
font-weight:700;
margin:0 0 12px;
color:#bb86fc
}

.orders-show-item-description {
font-size:14px;
color:#e0e0e0;
margin-bottom:15px;
line-height:1.6
}

.orders-show-item-meta {
display:flex;
flex-wrap:wrap;
gap:15px
}

.orders-show-item-quantity {
font-size:14px;
color:#121212;
background-color:#ffd166;
padding:6px 12px;
border-radius:20px;
display:inline-block;
font-size:14px;
font-weight:700
}

.orders-show-item-delivery {
font-size:14px;
color:#121212;
background-color:#ef476f;
padding:6px 12px;
border-radius:20px;
display:inline-block;
font-size:14px;
font-weight:700
}

.orders-show-item-price {
display:flex;
flex-direction:column;
align-items:flex-end;
margin-left:20px;
min-width:120px
}

.orders-show-item-price-label {
font-size:14px;
color:#a0a0a0;
margin-bottom:5px
}

.orders-show-item-price-value {
font-size:18px;
font-weight:700;
color:#bb86fc
}

.orders-show-message-content {
width:100%;
display:flex;
justify-content:center
}

.orders-show-message-textarea {
width:80%;
min-height:150px;
padding:15px;
border:1px solid #3c3c3c;
border-radius:8px;
font-family:monospace;
font-size:14px;
resize:vertical;
background-color:#2c2c2c;
color:#e0e0e0;
transition:all .3s ease
}

.orders-show-message-textarea:hover {
border-color:#bb86fc
}

.orders-show-message-textarea:focus {
outline:none;
border-color:#bb86fc
}
</style>
@endsection
