@extends('layouts.app')
@section('content')

<div class="disputes-index-container">
    <div class="disputes-index-card">
        <h1 class="disputes-index-title">My Disputes</h1>
        {{-- Disputes List --}}
        <div>
            @if($disputes->isEmpty())
                <div class="disputes-index-empty">
                    <p>You don't have any disputes at the moment.</p>
                    <a href="{{ route('vendor.sales') }}" class="disputes-index-back-btn">Return to Sales</a>
                </div>
            @else
                <div class="disputes-index-table-container">
                    <table class="disputes-index-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Buyer</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($disputes as $dispute)
                                <tr>
                                    <td>{{ substr($dispute->order->id, 0, 8) }}</td>
                                    <td>{{ $dispute->created_at->format('Y-m-d / H:i') }}</td>
                                    <td>{{ $dispute->order->user->username }}</td>
                                    <td>{{ Str::limit($dispute->reason, 30) }}</td>
                                    <td>
                                        <span class="disputes-index-status disputes-index-status-{{ $dispute->status }}">
                                            {{ $dispute->getFormattedStatus() }}
                                        </span>
                                        @if($dispute->resolved_at)
                                            <div class="vendor-dispute-index-resolution">
                                                {{ $dispute->resolved_at->format('Y-m-d / H:i') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('vendor.disputes.show', $dispute->id) }}" class="disputes-index-action-btn">
                                            View Dispute
                                        </a>
                                        <a href="{{ route('vendor.sales.show', $dispute->order->unique_url) }}" class="disputes-index-action-btn">
                                            View Order
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
