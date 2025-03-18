@extends('layouts.app')

@section('content')
<div class="admin-disputes-show-container">
    <div class="admin-disputes-show-header">
        <h1 class="admin-disputes-show-title">Dispute Management</h1>
        <div class="admin-disputes-show-back">
            <a href="{{ route('admin.disputes.index') }}" class="admin-disputes-show-back-btn">
                Return to Disputes
            </a>
        </div>
    </div>

    {{-- Dispute Info --}}
    <div class="admin-disputes-show-info-container">
        <div class="admin-disputes-show-info-card admin-disputes-show-status-{{ $dispute->status }}">
            <div class="admin-disputes-show-info-header">
                <h2 class="admin-disputes-show-info-title">Dispute Information</h2>
                <div class="admin-disputes-show-info-status">
                    Status: <span class="admin-disputes-show-status-badge">{{ $dispute->getFormattedStatus() }}</span>
                </div>
            </div>
            
            <div class="admin-disputes-show-info-grid">
                <div class="admin-disputes-show-info-item">
                    <div class="admin-disputes-show-info-label">Order ID</div>
                    <div class="admin-disputes-show-info-value">{{ substr($dispute->order->id, 0, 8) }}</div>
                </div>
                <div class="admin-disputes-show-info-item">
                    <div class="admin-disputes-show-info-label">Dispute Created</div>
                    <div class="admin-disputes-show-info-value">{{ $dispute->created_at->format('Y-m-d / H:i') }}</div>
                </div>
                <div class="admin-disputes-show-info-item">
                    <div class="admin-disputes-show-info-label">Buyer</div>
                    <div class="admin-disputes-show-info-value">{{ $dispute->order->user->username }}</div>
                </div>
                <div class="admin-disputes-show-info-item">
                    <div class="admin-disputes-show-info-label">Vendor</div>
                    <div class="admin-disputes-show-info-value">{{ $dispute->order->vendor->username }}</div>
                </div>
                <div class="admin-disputes-show-info-item admin-dispute-show-info-item-reason">
                    <div class="admin-disputes-show-info-label">Reason for Dispute</div>
                    <div class="admin-disputes-show-info-value">{{ $dispute->reason }}</div>
                </div>
                @if($dispute->resolved_at)
                    <div class="admin-disputes-show-info-item">
                        <div class="admin-disputes-show-info-label">Resolved On</div>
                        <div class="admin-disputes-show-info-value">{{ $dispute->resolved_at->format('Y-m-d / H:i') }}</div>
                    </div>
                    <div class="admin-disputes-show-info-item">
                        <div class="admin-disputes-show-info-label">Resolved By</div>
                        <div class="admin-disputes-show-info-value">{{ $dispute->resolver->username }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Resolution Actions (Only for active disputes) --}}
    @if($dispute->status === \App\Models\Dispute::STATUS_ACTIVE)
        <div class="admin-disputes-show-resolution-container">
            <div class="admin-disputes-show-resolution-card">
                <h2 class="admin-disputes-show-resolution-title">Resolve Dispute</h2>
                <div class="admin-disputes-show-resolution-desc">
                    <p>Please select a resolution for this dispute. This action cannot be undone.</p>
                    <ul>
                        <li><strong>Vendor Prevails:</strong> Order will be marked as completed.</li>
                        <li><strong>Buyer Prevails:</strong> Order will be marked as cancelled.</li>
                    </ul>
                </div>
                
                <div class="admin-disputes-show-resolution-actions">
                    <form action="{{ route('admin.disputes.vendor-prevails', $dispute->id) }}" method="POST" class="admin-disputes-show-resolution-form">
                        @csrf
                        <div class="admin-disputes-show-resolution-message-field">
                            <label for="vendor-message" class="admin-disputes-show-resolution-message-label">Resolution Message (Optional)</label>
                            <textarea 
                                id="vendor-message" 
                                name="message" 
                                class="admin-disputes-show-resolution-message-textarea" 
                                placeholder="Explain why the vendor prevails..."></textarea>
                        </div>
                        <button type="submit" class="admin-disputes-show-resolution-btn admin-disputes-show-vendor-btn">
                            Resolve: Vendor Prevails
                        </button>
                    </form>
                    
                    <form action="{{ route('admin.disputes.buyer-prevails', $dispute->id) }}" method="POST" class="admin-disputes-show-resolution-form">
                        @csrf
                        <div class="admin-disputes-show-resolution-message-field">
                            <label for="buyer-message" class="admin-disputes-show-resolution-message-label">Resolution Message (Optional)</label>
                            <textarea 
                                id="buyer-message" 
                                name="message" 
                                class="admin-disputes-show-resolution-message-textarea" 
                                placeholder="Explain why the buyer prevails..."></textarea>
                        </div>
                        <button type="submit" class="admin-disputes-show-resolution-btn admin-disputes-show-buyer-btn">
                            Resolve: Buyer Prevails
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Dispute Chat --}}
    <div class="admin-disputes-show-chat-container">
        <div class="admin-disputes-show-chat-card">
            <h2 class="admin-disputes-show-chat-title">Dispute Messages</h2>
            
            <div class="admin-disputes-show-chat-messages">
                @if($dispute->messages->isEmpty())
                    <div class="admin-disputes-show-chat-empty">
                        No messages in this dispute yet.
                    </div>
                @else
                    @foreach($dispute->messages as $message)
                        <div class="admin-disputes-show-chat-message admin-disputes-show-message-{{ $message->getMessageType() }}">
                            <div class="admin-disputes-show-chat-message-header">
                                <div class="admin-disputes-show-chat-message-sender">
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
                                <div class="admin-disputes-show-chat-message-time">
                                    {{ $message->created_at->format('Y-m-d / H:i') }}
                                </div>
                            </div>
                            <div class="admin-disputes-show-chat-message-content">
                                {{ $message->message }}
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            
            {{-- Message Form (Only for active disputes) --}}
            @if($dispute->status === \App\Models\Dispute::STATUS_ACTIVE)
                <div class="admin-disputes-show-chat-form-container">
                    <form action="{{ route('disputes.add-message', $dispute->id) }}" method="POST" class="admin-disputes-show-chat-form">
                        @csrf
                        <div class="admin-disputes-show-chat-form-field">
                            <label for="message" class="admin-disputes-show-chat-form-label">Add a Message</label>
                            <textarea 
                                id="message" 
                                name="message" 
                                class="admin-disputes-show-chat-form-textarea" 
                                placeholder="Type your message here..." 
                                required 
                                minlength="1" 
                                maxlength="1000"></textarea>
                        </div>
                        <div class="admin-disputes-show-chat-form-actions">
                            <button type="submit" class="admin-disputes-show-chat-form-submit-btn">Send Message</button>
                        </div>
                    </form>
                </div>
            @else
                <div class="admin-disputes-show-chat-closed">
                    <p>This dispute has been resolved. No new messages can be added.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection