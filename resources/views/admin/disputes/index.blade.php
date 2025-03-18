@extends('layouts.app')

@section('content')
<div class="admin-disputes-index-container">
    <div class="admin-disputes-index-header">
        <h1 class="admin-disputes-index-title">Dispute Management</h1>
    </div>

    <div class="admin-disputes-tabs">
        <div class="admin-disputes-tab active" data-tab="active">Active Disputes</div>
        <div class="admin-disputes-tab" data-tab="resolved">Resolved Disputes</div>
    </div>

    {{-- Active Disputes --}}
    <div class="admin-disputes-tab-content" id="active-tab-content">
        @if($activeDisputes->isEmpty())
            <div class="admin-disputes-empty">
                <p>There are no active disputes at the moment.</p>
            </div>
        @else
            <div class="admin-disputes-list">
                @foreach($activeDisputes as $dispute)
                    <div class="admin-disputes-item">
                        <div class="admin-disputes-item-header">
                            <div class="admin-disputes-item-id">
                                Dispute ID: {{ substr($dispute->id, 0, 8) }}
                            </div>
                            <div class="admin-disputes-item-date">
                                Opened: {{ $dispute->created_at->format('Y-m-d / H:i') }}
                            </div>
                        </div>
                        
                        <div class="admin-disputes-item-users">
                            <div class="admin-disputes-item-buyer">
                                Buyer: {{ $dispute->order->user->username }}
                            </div>
                            <div class="admin-disputes-item-vendor">
                                Vendor: {{ $dispute->order->vendor->username }}
                            </div>
                        </div>
                        
                        <div class="admin-disputes-item-order">
                            Order ID: {{ substr($dispute->order->id, 0, 8) }}
                        </div>
                        
                        <div class="admin-disputes-item-reason">
                            <strong>Reason:</strong> {{ Str::limit($dispute->reason, 100) }}
                        </div>
                        
                        <div class="admin-disputes-item-actions">
                            <a href="{{ route('admin.disputes.show', $dispute->id) }}" class="admin-disputes-item-view-btn">
                                Manage Dispute
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Resolved Disputes --}}
    <div class="admin-disputes-tab-content hidden" id="resolved-tab-content">
        @if($resolvedDisputes->isEmpty())
            <div class="admin-disputes-empty">
                <p>There are no resolved disputes at the moment.</p>
            </div>
        @else
            <div class="admin-disputes-list">
                @foreach($resolvedDisputes as $dispute)
                    <div class="admin-disputes-item admin-disputes-item-status-{{ $dispute->status }}">
                        <div class="admin-disputes-item-header">
                            <div class="admin-disputes-item-id">
                                Dispute ID: {{ substr($dispute->id, 0, 8) }}
                            </div>
                            <div class="admin-disputes-item-dates">
                                <div>Opened: {{ $dispute->created_at->format('Y-m-d / H:i') }}</div>
                                <div>Resolved: {{ $dispute->resolved_at->format('Y-m-d / H:i') }}</div>
                            </div>
                        </div>
                        
                        <div class="admin-disputes-item-users">
                            <div class="admin-disputes-item-buyer">
                                Buyer: {{ $dispute->order->user->username }}
                            </div>
                            <div class="admin-disputes-item-vendor">
                                Vendor: {{ $dispute->order->vendor->username }}
                            </div>
                        </div>
                        
                        <div class="admin-disputes-item-order">
                            Order ID: {{ substr($dispute->order->id, 0, 8) }}
                        </div>
                        
                        <div class="admin-disputes-item-status">
                            Resolution: <span class="admin-disputes-status-badge">{{ $dispute->getFormattedStatus() }}</span>
                            <span class="admin-disputes-resolver">by {{ $dispute->resolver->username }}</span>
                        </div>
                        
                        <div class="admin-disputes-item-actions">
                            <a href="{{ route('admin.disputes.show', $dispute->id) }}" class="admin-disputes-item-view-btn">
                                View Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Tab Switching JavaScript --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.admin-disputes-tab');
            const tabContents = document.querySelectorAll('.admin-disputes-tab-content');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    // Remove active class from all tabs
                    tabs.forEach(t => t.classList.remove('active'));
                    
                    // Add active class to clicked tab
                    tab.classList.add('active');
                    
                    // Hide all tab contents
                    tabContents.forEach(content => content.classList.add('hidden'));
                    
                    // Show the selected tab content
                    const tabContentId = tab.dataset.tab + '-tab-content';
                    document.getElementById(tabContentId).classList.remove('hidden');
                });
            });
        });
    </script>
</div>
@endsection