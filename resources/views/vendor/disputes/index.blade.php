@extends('layouts.app')

@section('content')
<div class="vendor-disputes-index-container">
    <div class="vendor-disputes-index-header">
        <h1 class="vendor-disputes-index-title">Disputes</h1>
    </div>

    @if($disputes->isEmpty())
        <div class="vendor-disputes-index-empty">
            <p>You don't have any disputes at the moment.</p>
            <a href="{{ route('vendor.sales') }}" class="vendor-disputes-index-back-btn">Return to Sales</a>
        </div>
    @else
        <div class="vendor-disputes-index-list">
            @foreach($disputes as $dispute)
                <div class="vendor-disputes-index-item vendor-disputes-index-status-{{ $dispute->status }}">
                    <div class="vendor-disputes-index-item-header">
                        <div class="vendor-disputes-index-item-order">
                            Order ID: {{ substr($dispute->order->id, 0, 8) }}
                        </div>
                        <div class="vendor-disputes-index-item-date">
                            Opened: {{ $dispute->created_at->format('Y-m-d / H:i') }}
                        </div>
                    </div>
                    
                    <div class="vendor-disputes-index-item-buyer">
                        Buyer: {{ $dispute->order->user->username }}
                    </div>
                    
                    <div class="vendor-disputes-index-item-status">
                        Status: <span class="vendor-disputes-index-status-badge">{{ $dispute->getFormattedStatus() }}</span>
                    </div>
                    
                    @if($dispute->resolved_at)
                        <div class="vendor-disputes-index-item-resolution">
                            Resolved: {{ $dispute->resolved_at->format('Y-m-d / H:i') }}
                        </div>
                    @endif
                    
                    <div class="vendor-disputes-index-item-reason">
                        <strong>Reason:</strong> {{ Str::limit($dispute->reason, 100) }}
                    </div>
                    
                    <div class="vendor-disputes-index-item-actions">
                        <a href="{{ route('vendor.disputes.show', $dispute->id) }}" class="vendor-disputes-index-item-view-btn">
                            View Dispute
                        </a>
                        <a href="{{ route('vendor.sales.show', $dispute->order->unique_url) }}" class="vendor-disputes-index-item-order-btn">
                            View Order
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection