@extends('layouts.app')

@section('content')
<div class="sales-container">
    {{-- Breadcrumb Navigation --}}
    <div class="sales-breadcrumb">
        <a href="{{ route('vendor.index') }}">Vendor Dashboard</a>
        <span>/</span>
        <span class="sales-breadcrumb-current">Sales</span>
    </div>

    <div class="sales-header">
        <h1 class="sales-title">My Sales</h1>
    </div>

    {{-- Sales List --}}
    <div class="sales-list">
        @if($sales->isEmpty())
            <div class="sales-empty">
                <p>You don't have any sales yet.</p>
            </div>
        @else
            <div class="sales-table-container">
                <table class="sales-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Buyer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $sale)
                            <tr>
                                <td>{{ substr($sale->id, 0, 8) }}</td>
                                <td>{{ $sale->created_at->format('M d, Y') }}</td>
                                <td>{{ $sale->user->username }}</td>
                                <td>${{ number_format($sale->total, 2) }}</td>
                                <td>
                                    <span class="sale-status sale-status-{{ $sale->status }}">
                                        {{ $sale->getFormattedStatus() }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('vendor.sales.show', $sale->unique_url) }}" class="btn-sale-details">
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
    .sales-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .sales-breadcrumb {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        font-size: 14px;
    }
    
    .sales-breadcrumb a {
        color: #666;
        text-decoration: none;
    }
    
    .sales-breadcrumb span {
        margin: 0 10px;
        color: #999;
    }
    
    .sales-breadcrumb-current {
        color: #333;
        font-weight: 600;
    }
    
    .sales-header {
        margin-bottom: 30px;
    }
    
    .sales-title {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 10px;
    }
    
    .sales-empty {
        text-align: center;
        padding: 50px 0;
    }
    
    .sales-empty p {
        margin-bottom: 20px;
        font-size: 16px;
        color: #666;
    }
    
    .sales-table-container {
        overflow-x: auto;
    }
    
    .sales-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .sales-table th, 
    .sales-table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .sales-table th {
        background-color: #f8fafc;
        font-weight: 600;
        font-size: 14px;
        color: #4a5568;
    }
    
    .sale-status {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .sale-status-waiting_payment {
        background-color: #fed7d7;
        color: #c53030;
    }
    
    .sale-status-payment_received {
        background-color: #feebc8;
        color: #c05621;
    }
    
    .sale-status-product_delivered {
        background-color: #c6f6d5;
        color: #2f855a;
    }
    
    .sale-status-completed {
        background-color: #bee3f8;
        color: #2b6cb0;
    }
    
    .btn-sale-details {
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