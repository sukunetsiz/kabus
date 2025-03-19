@extends('layouts.app')
@section('content')

<div class="disputes-index-container">
    <div class="disputes-index-card">
        <h1 class="disputes-index-title">Dispute Management</h1>
        {{-- Disputes List --}}
        <div>
            @if($disputes->isEmpty())
                <div class="disputes-index-empty">
                    <p>There are no disputes at the moment.</p>
                </div>
            @else
                <div class="disputes-index-table-container">
                    <table class="disputes-index-table">
                        <thead>
                            <tr>
                                <th>Dispute ID</th>
                                <th>Date Opened</th>
                                <th>Order ID</th>
                                <th>Buyer</th>
                                <th>Vendor</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($disputes as $dispute)
                                <tr>
                                    <td>{{ substr($dispute->id, 0, 8) }}</td>
                                    <td>{{ $dispute->created_at->format('Y-m-d / H:i') }}</td>
                                    <td>{{ substr($dispute->order->id, 0, 8) }}</td>
                                    <td>{{ $dispute->order->user->username }}</td>
                                    <td>{{ $dispute->order->vendor->username }}</td>
                                    <td>
                                        <span class="disputes-index-status disputes-index-status-{{ $dispute->status }}">
                                            {{ $dispute->getFormattedStatus() }}
                                        </span>
                                        @if($dispute->status !== \App\Models\Dispute::STATUS_ACTIVE)
                                            <div class="admin-dispute-index-resolver">
                                                by {{ $dispute->resolver->username }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($dispute->status === \App\Models\Dispute::STATUS_ACTIVE)
                                            <a href="{{ route('admin.disputes.show', $dispute->id) }}" class="disputes-index-action-btn">
                                                Manage Dispute
                                            </a>
                                        @else
                                            <a href="{{ route('admin.disputes.show', $dispute->id) }}" class="disputes-index-action-btn">
                                                View Details
                                            </a>
                                        @endif
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
