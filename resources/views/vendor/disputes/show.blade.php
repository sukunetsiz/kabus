@extends('layouts.app')

@section('content')
<div class="vendor-disputes-show-container">
    <div class="vendor-disputes-show-header">
        <h1 class="vendor-disputes-show-title">Dispute Details</h1>
        <div class="vendor-disputes-show-back">
            <a href="{{ route('vendor.disputes.index') }}" class="vendor-disputes-show-back-btn">
                Return to Disputes
            </a>
        </div>
    </div>

    {{-- Dispute Info --}}
    <div class="vendor-disputes-show-info-container">
        <div class="vendor-disputes-show-info-card vendor-disputes-show-status-{{ $dispute->status }}">
            <div class="vendor-disputes-show-info-header">
                <h2 class="vendor-disputes-show-info-title">Order Information</h2>
                <div class="vendor-disputes-show-info-status">
                    Status: <span class="vendor-disputes-show-status-badge">{{ $dispute->getFormattedStatus() }}</span>
                </div>
            </div>
            
            <div class="vendor-disputes-show-info-grid">
                <div class="vendor-disputes-show-info-item">
                    <div class="vendor-disputes-show-info-label">Order ID</div>
                    <div class="vendor-disputes-show-info-value">{{ substr($dispute->order->id, 0, 8) }}</div>
                </div>
                <div class="vendor-disputes-show-info-item">
                    <div class="vendor-disputes-show-info-label">Created</div>
                    <div class="vendor-disputes-show-info-value">{{ $dispute->created_at->format('Y-m-d / H:i') }}</div>
                </div>
                <div class="vendor-disputes-show-info-item">
                    <div class="vendor-disputes-show-info-label">Buyer</div>
                    <div class="vendor-disputes-show-info-value">{{ $dispute->order->user->username }}</div>
                </div>
                <div class="vendor-disputes-show-info-item vendor-dispute-show-info-item-reason">
                    <div class="vendor-disputes-show-info-label">Reason for Dispute</div>
                    <div class="vendor-disputes-show-info-value">{{ $dispute->reason }}</div>
                </div>
                @if($dispute->resolved_at)
                    <div class="vendor-disputes-show-info-item">
                        <div class="vendor-disputes-show-info-label">Resolved On</div>
                        <div class="vendor-disputes-show-info-value">{{ $dispute->resolved_at->format('Y-m-d / H:i') }}</div>
                    </div>
                @endif
            </div>
            
            <div class="vendor-disputes-show-info-actions">
                <a href="{{ route('vendor.sales.show', $dispute->order->unique_url) }}" class="vendor-disputes-show-info-order-btn">
                    View Order Details
                </a>
            </div>
        </div>
    </div>

    {{-- Dispute Chat --}}
    <div class="vendor-disputes-show-chat-container">
        <div class="vendor-disputes-show-chat-card">
            <h2 class="vendor-disputes-show-chat-title">Dispute Messages</h2>
            
            <div class="vendor-disputes-show-chat-messages">
                @if($dispute->messages->isEmpty())
                    <div class="vendor-disputes-show-chat-empty">
                        No messages in this dispute yet.
                    </div>
                @else
                    @foreach($dispute->messages as $message)
                        <div class="vendor-disputes-show-chat-message vendor-disputes-show-message-{{ $message->getMessageType() }}">
                            <div class="vendor-disputes-show-chat-message-header">
                                <div class="vendor-disputes-show-chat-message-sender">
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
                                <div class="vendor-disputes-show-chat-message-time">
                                    {{ $message->created_at->format('Y-m-d / H:i') }}
                                </div>
                            </div>
                            <div class="vendor-disputes-show-chat-message-content">
                                {{ $message->message }}
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            
            {{-- Message Form --}}
            @if($dispute->status === \App\Models\Dispute::STATUS_ACTIVE)
                <div class="vendor-disputes-show-chat-form-container">
                    <form action="{{ route('disputes.add-message', $dispute->id) }}" method="POST" class="vendor-disputes-show-chat-form">
                        @csrf
                        <div class="vendor-disputes-show-chat-form-field">
                            <label for="message" class="vendor-disputes-show-chat-form-label">Add a Message</label>
                            <textarea 
                                id="message" 
                                name="message" 
                                class="vendor-disputes-show-chat-form-textarea" 
                                placeholder="Type your message here..." 
                                required 
                                minlength="1" 
                                maxlength="1000"></textarea>
                        </div>
                        <div class="vendor-disputes-show-chat-form-actions">
                            <button type="submit" class="vendor-disputes-show-chat-form-submit-btn">Send Message</button>
                        </div>
                    </form>
                </div>
            @else
                <div class="vendor-disputes-show-chat-closed">
                    <p>This dispute has been resolved. No new messages can be added.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection