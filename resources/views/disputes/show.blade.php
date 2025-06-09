@extends('layouts.app')

@section('content')

<div class="disputes-show-container">
    <div class="disputes-show-header">
        <h1 class="disputes-show-title">Dispute Details</h1>
        <div>
            <a href="{{ route('disputes.index') }}" class="disputes-show-back-link">
                Return to Disputes
            </a>
        </div>
    </div>

    {{-- Dispute Info --}}
    <div class="disputes-show-card">
        <div>
            <div>
                <h2 class="disputes-show-section-title">Order Information</h2>
                <div class="disputes-show-status">
                    <span class="disputes-show-status-badge disputes-show-status-{{ strtolower($dispute->status) }}">
                        {{ $dispute->getFormattedStatus() }}
                    </span>
                </div>
            </div>
            
            <div class="disputes-show-info-grid">
                <div class="disputes-show-info-item">
                    <div class="disputes-show-info-label">Order ID</div>
                    <div class="disputes-show-info-value">{{ substr($dispute->order->id, 0, 8) }}</div>
                </div>
                <div class="disputes-show-info-item">
                    <div class="disputes-show-info-label">Created</div>
                    <div class="disputes-show-info-value">{{ $dispute->created_at->format('Y-m-d / H:i') }}</div>
                </div>
                <div class="disputes-show-info-item">
                    <div class="disputes-show-info-label">Buyer</div>
                    <div class="disputes-show-info-value">{{ $dispute->order->user->username }}</div>
                </div>
                <div class="disputes-show-info-item">
                    <div class="disputes-show-info-label">Vendor</div>
                    <div class="disputes-show-info-value">{{ $dispute->order->vendor->username }}</div>
                </div>
                <div class="disputes-show-info-item">
                    <div class="disputes-show-info-label">Reason for Dispute</div>
                    <div class="disputes-show-info-value">{{ $dispute->reason }}</div>
                </div>
                @if($dispute->resolved_at)
                    <div class="disputes-show-info-item">
                        <div class="disputes-show-info-label">Resolved On</div>
                        <div class="disputes-show-info-value">{{ $dispute->resolved_at->format('Y-m-d / H:i') }}</div>
                    </div>
                @endif
            </div>
            
            <div style="text-align: center;">
                <a href="{{ route('orders.show', $dispute->order->unique_url) }}" class="disputes-show-order-btn">
                    View Order Details
                </a>
            </div>
        </div>
    </div>

    {{-- Dispute Chat --}}
    <div class="disputes-show-card">
        <div>
            <h2 class="disputes-show-section-title">Dispute Messages</h2>
            
            <div class="disputes-show-messages-list">
                @if($dispute->messages->isEmpty())
                    <div class="disputes-show-empty-message">
                        No messages in this dispute yet.
                    </div>
                @else
                    @foreach($dispute->messages as $message)
                        <div class="disputes-show-message 
                            @if($message->isFromAdmin())
                                disputes-show-message-admin
                            @elseif($message->isFromBuyer())
                                disputes-show-message-buyer
                            @elseif($message->isFromVendor())
                                disputes-show-message-vendor
                            @endif
                        ">
                            <div class="disputes-show-message-header">
                                <div class="disputes-show-message-user">
                                    @if($message->isFromAdmin())
                                        Admin: {{ $message->user->username }}
                                    @elseif($message->isFromBuyer())
                                        Buyer: {{ $message->user->username }}
                                    @elseif($message->isFromVendor())
                                        Vendor: {{ $message->user->username }}
                                    @else
                                        {{ $message->user->username }}
                                    @endif
                                </div>
                                <div class="disputes-show-message-time">
                                    {{ $message->created_at->format('Y-m-d / H:i') }}
                                </div>
                            </div>
                            <div class="disputes-show-message-content">
                                {{ $message->message }}
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            
            {{-- Message Form --}}
            @if($dispute->status === \App\Models\Dispute::STATUS_ACTIVE)
                <div>
                    <form action="{{ route('disputes.add-message', $dispute->id) }}" method="POST">
                        @csrf
                        <div class="disputes-show-form-group">
                            <label for="message" class="disputes-show-form-label">Add a Message</label>
                            <textarea 
                                id="message" 
                                name="message" 
                                class="disputes-show-textarea"
                                placeholder="Type your message here..." 
                                required 
                                minlength="4" 
                                maxlength="800"></textarea>
                        </div>
                        <div>
                            <button type="submit" class="disputes-show-submit-btn">Send Message</button>
                        </div>
                    </form>
                </div>
            @else
                <div class="disputes-show-resolved-message">
                    <p>This dispute has been resolved. No new messages can be added.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
