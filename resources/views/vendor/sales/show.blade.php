@extends('layouts.app')

@section('content')
<div>
    <div>
        <h1>Sale Details</h1>
        <div>ID: {{ substr($sale->id, 0, 8) }}</div>
    </div>

    {{-- Order Status --}}
    <div>
        <div>
            <h2>Status: {{ $sale->getFormattedStatus() }}</h2>
            
            <div>
                <div>
                    <div>1</div>
                    <div>Waiting for Payment</div>
                    @if($sale->paid_at)
                        <div>{{ $sale->paid_at->format('M d, Y') }}</div>
                    @endif
                </div>
                <div>
                    <div>2</div>
                    <div>Payment Received</div>
                    @if($sale->delivered_at)
                        <div>{{ $sale->delivered_at->format('M d, Y') }}</div>
                    @endif
                </div>
                <div>
                    <div>3</div>
                    <div>Product Delivered</div>
                    @if($sale->completed_at)
                        <div>{{ $sale->completed_at->format('M d, Y') }}</div>
                    @endif
                </div>
                <div>
                    <div>4</div>
                    <div>Order Completed</div>
                </div>
            </div>

            {{-- Status-based Actions for Vendor --}}
            @if($sale->status === 'payment_received')
                <div>
                    <form action="{{ route('orders.mark-delivered', $sale->unique_url) }}" method="POST">
                        @csrf
                        <button type="submit">Product Sent</button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    {{-- Buyer Information --}}
    <div>
        <div>
            <h2>Buyer Information</h2>
            
            <div>
                <div>
                    <div>Username</div>
                    <div>{{ $sale->user->username }}</div>
                </div>
                <div>
                    <div>Order Date</div>
                    <div>{{ $sale->created_at->format('M d, Y h:i A') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Order Details --}}
    <div>
        <div>
            <h2>Sale Information</h2>
            
            <div>
                <div>
                    <div>Subtotal</div>
                    <div>${{ number_format($sale->subtotal, 2) }}</div>
                </div>
                <div>
                    <div>Commission</div>
                    <div>${{ number_format($sale->commission, 2) }}</div>
                </div>
                <div>
                    <div>Total</div>
                    <div>${{ number_format($sale->total, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sale Items --}}
    <div>
        <div>
            <h2>Items</h2>
            
            <div>
                @foreach($sale->items as $item)
                    <div>
                        <div>
                            <h3>{{ $item->product_name }}</h3>
                            <div>{{ Str::limit($item->product_description, 200) }}</div>
                            
                            <div>
                                @if($item->bulk_option)
                                    <div>
                                        {{ $item->quantity }} sets of {{ $item->bulk_option['amount'] ?? 0 }} 
                                        (Total: {{ $item->quantity * ($item->bulk_option['amount'] ?? 1) }})
                                    </div>
                                @else
                                    <div>
                                        Quantity: {{ $item->quantity }}
                                    </div>
                                @endif
                                
                                @if($item->delivery_option)
                                    <div>
                                        Delivery: {{ $item->delivery_option['description'] ?? 'N/A' }}
                                        ({{ isset($item->delivery_option['price']) ? '$' . number_format($item->delivery_option['price'], 2) : 'N/A' }})
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <div>Price:</div>
                            <div>${{ number_format($item->getTotalPrice(), 2) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Message Section --}}
    @if($sale->encrypted_message)
        <div>
            <div>
                <h2>Encrypted Message from Buyer</h2>
                <div>
                    <textarea readonly>{{ $sale->encrypted_message }}</textarea>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
