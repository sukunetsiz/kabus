@extends('layouts.app')
@section('content')

<div class="sales-index-container">
    <div class="sales-index-card">
        <h1 class="sales-index-title">My Sales</h1>
        {{-- Sales List --}}
        <div>
            @if($sales->isEmpty())
                <div class="sales-index-empty">
                    <p>You don't have any sales yet.</p>
                </div>
            @else
                <div class="sales-index-table-container">
                    <table class="sales-index-table">
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
                                        <span class="sales-index-status sales-index-status-{{ strtolower($sale->status) }}">
                                            {{ $sale->getFormattedStatus() }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('vendor.sales.show', $sale->unique_url) }}" class="sales-index-action-btn">
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
