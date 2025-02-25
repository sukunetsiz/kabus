@extends('layouts.app')

@section('content')
<div class="orders-container">
    {{-- Breadcrumb Navigation --}}
    <div class="orders-breadcrumb">
        <a href="{{ route('home') }}">Home</a>
        <span>/</span>
        <span class="orders-breadcrumb-current">My Orders</span>
    </div>

    <div class="orders-header">
        <h1 class="orders-title">My Orders</h1>
    </div>

    {{-- Orders List --}}
    <div class="orders-list">
        @if($orders->isEmpty())
            <div class="orders-empty">
                <p>You don't have any orders yet.</p>
                <a href="{{ route('products.index') }}" class="btn-primary">Browse Products</a>
            </div>
        @else
            <div class="orders-table-container">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Vendor</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>{{ substr($order->id, 0, 8) }}</td>
                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                <td>{{ $order->vendor->username }}</td>
                                <td>${{ number_format($order->total, 2) }}</td>
                                <td>
                                    <span class="order-status order-status-{{ $order->status }}">
                                        {{ $order->getFormattedStatus() }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('orders.show', $order->unique_url) }}" class="btn-order-details">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<style>
    .orders-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .orders-breadcrumb {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        font-size: 14px;
    }
    
    .orders-breadcrumb a {
        color: #666;
        text-decoration: none;
    }
    
    .orders-breadcrumb span {
        margin: 0 10px;
        color: #999;
    }
    
    .orders-breadcrumb-current {
        color: #333;
        font-weight: 600;
    }
    
    .orders-header {
        margin-bottom: 30px;
    }
    
    .orders-title {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 10px;
    }
    
    .orders-empty {
        text-align: center;
        padding: 50px 0;
    }
    
    .orders-empty p {
        margin-bottom: 20px;
        font-size: 16px;
        color: #666;
    }
    
    .btn-primary {
        display: inline-block;
        padding: 10px 20px;
        background-color: #4a5568;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        font-weight: 500;
    }
    
    .orders-table-container {
        overflow-x: auto;
    }
    
    .orders-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .orders-table th, 
    .orders-table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .orders-table th {
        background-color: #f8fafc;
        font-weight: 600;
        font-size: 14px;
        color: #4a5568;
    }
    
    .order-status {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .order-status-waiting_payment {
        background-color: #fed7d7;
        color: #c53030;
    }
    
    .order-status-payment_received {
        background-color: #feebc8;
        color: #c05621;
    }
    
    .order-status-product_delivered {
        background-color: #c6f6d5;
        color: #2f855a;
    }
    
    .order-status-completed {
        background-color: #bee3f8;
        color: #2b6cb0;
    }
    
    .btn-order-details {
        display: inline-block;
        padding: 6px 12px;
        background-color: #4a5568;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }
</style>
@endsection