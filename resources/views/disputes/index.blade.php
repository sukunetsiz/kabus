@extends('layouts.app')

@section('content')
<div class="disputes-index-container">
    <div class="disputes-index-header">
        <h1 class="disputes-index-title">My Disputes</h1>
    </div>

    @if($disputes->isEmpty())
        <div class="disputes-index-empty">
            <p>You don't have any disputes at the moment.</p>
            <a href="{{ route('orders.index') }}" class="disputes-index-back-btn">Return to Orders</a>
        </div>
    @else
        <div class="disputes-index-list">
            @foreach($disputes as $dispute)
                <div class="disputes-index-item disputes-index-status-{{ $dispute->status }}">
                    <div class="disputes-index-item-header">
                        <div class="disputes-index-item-order">
                            Order ID: {{ substr($dispute->order->id, 0, 8) }}
                        </div>
                        <div class="disputes-index-item-date">
                            Opened: {{ $dispute->created_at->format('Y-m-d / H:i') }}
                        </div>
                    </div>
                    
                    <div class="disputes-index-item-vendor">
                        Vendor: {{ $dispute->order->vendor->username }}
                    </div>
                    
                    <div class="disputes-index-item-status">
                        Status: <span class="disputes-index-status-badge">{{ $dispute->getFormattedStatus() }}</span>
                    </div>
                    
                    @if($dispute->resolved_at)
                        <div class="disputes-index-item-resolution">
                            Resolved: {{ $dispute->resolved_at->format('Y-m-d / H:i') }}
                        </div>
                    @endif
                    
                    <div class="disputes-index-item-reason">
                        <strong>Reason:</strong> {{ Str::limit($dispute->reason, 100) }}
                    </div>
                    
                    <div class="disputes-index-item-actions">
                        <a href="{{ route('disputes.show', $dispute->id) }}" class="disputes-index-item-view-btn">
                            View Dispute
                        </a>
                        <a href="{{ route('orders.show', $dispute->order->unique_url) }}" class="disputes-index-item-order-btn">
                            View Order
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection