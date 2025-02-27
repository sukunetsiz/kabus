@extends('layouts.app')

@section('content')

<div class="orders-index-container">
    <div class="orders-index-card">
        <h1 class="orders-index-title">My Orders</h1>

        {{-- Orders List --}}
        <div>
            @if($orders->isEmpty())
                <div class="orders-index-empty">
                    <p>You don't have any orders yet.</p>
                    <a href="{{ route('products.index') }}" class="orders-index-browse-btn">Browse Products</a>
                </div>
            @else
                <div class="orders-index-table-container">
                    <table class="orders-index-table">
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
                                    <td>{{ $order->created_at->format('Y-m-d / H:i') }}</td>
                                    <td>{{ $order->vendor->username }}</td>
                                    <td>${{ number_format($order->total, 2) }}</td>
                                    <td>
                                        <span class="orders-index-status orders-index-status-{{ strtolower($order->status) }}">
                                            {{ $order->getFormattedStatus() }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('orders.show', $order->unique_url) }}" class="orders-index-action-btn">
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
</div>
@endsection

