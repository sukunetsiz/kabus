@extends('layouts.app')

@section('content')
<style>
/* Custom Styles for Dispute Show Page */
.disputes-show-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
}

.disputes-show-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #bb86fc;
}

.disputes-show-title {
    color: #bb86fc;
    font-size: 28px;
    font-weight: 700;
    margin: 0;
}

.disputes-show-back-link {
    background-color: #292929;
    color: #bb86fc;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 700;
    transition: all 0.3s ease;
    display: inline-block;
}

.disputes-show-back-link:hover {
    background-color: #bb86fc;
    color: #121212;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(187, 134, 252, 0.3);
}

.disputes-show-card {
    background-color: #1e1e1e;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 30px;
    box-shadow: 0 6px 25px rgba(0, 0, 0, 0.3);
    border: 1px solid #3c3c3c;
}

.disputes-show-section-title {
    color: #bb86fc;
    font-size: 24px;
    margin-bottom: 20px;
    font-weight: 700;
    text-align: center;
}

.disputes-show-status {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}

.disputes-show-status-badge {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 700;
    font-size: 0.9em;
    text-transform: uppercase;
    margin-top: 10px;
}

.disputes-show-status-active {
    background-color: #E53B69;
    color: #121212;
}

.disputes-show-status-vendor_prevails {
    background-color: #1D84B5;
    color: #121212;
}

.disputes-show-status-buyer_prevails {
    background-color: #FFB627;
    color: #121212;
}

.disputes-show-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.disputes-show-info-item {
    background-color: #292929;
    border-radius: 8px;
    padding: 15px;
    transition: all 0.3s ease;
}

.disputes-show-info-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(187, 134, 252, 0.2);
}

.disputes-show-info-label {
    color: #a0a0a0;
    font-size: 14px;
    margin-bottom: 8px;
}

.disputes-show-info-value {
    color: #e0e0e0;
    font-size: 16px;
    font-weight: 600;
}

.disputes-show-order-btn {
    background-color: #bb86fc;
    color: #121212;
    padding: 12px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 700;
    transition: all 0.3s ease;
    display: inline-block;
    margin-top: 15px;
    text-align: center;
}

.disputes-show-order-btn:hover {
    background-color: #96c;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(187, 134, 252, 0.4);
}

.disputes-show-messages-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-bottom: 25px;
    max-height: 500px;
    overflow-y: auto;
    padding: 10px;
}

.disputes-show-message {
    background-color: #292929;
    border-radius: 8px;
    padding: 15px;
    transition: all 0.3s ease;
}

.disputes-show-message:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(187, 134, 252, 0.2);
}

.disputes-show-message-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    padding-bottom: 8px;
    border-bottom: 1px solid #3c3c3c;
}

.disputes-show-message-user {
    color: #bb86fc;
    font-weight: 700;
}

.disputes-show-message-admin {
    border-top: 4px solid #bb86fc;
    border-bottom: 4px solid #bb86fc;
}

.disputes-show-message-buyer {
    border-left: 4px solid #3498DB;
    border-right: 4px solid #3498DB;
}

.disputes-show-message-vendor {
    border-left: 4px solid #F39C12;
    border-right: 4px solid #F39C12;
}

.disputes-show-message-time {
    color: #a0a0a0;
    font-size: 14px;
}

.disputes-show-message-content {
    color: #e0e0e0;
    line-height: 1.5;
}

.disputes-show-empty-message {
    text-align: center;
    padding: 30px;
    color: #a0a0a0;
    font-style: italic;
    background-color: #292929;
    border-radius: 8px;
}

.disputes-show-form-group {
    margin-bottom: 20px;
}

.disputes-show-form-label {
    display: block;
    color: #bb86fc;
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 10px;
}

.disputes-show-textarea {
    width: 96%;
    min-height: 120px;
    padding: 15px;
    background-color: #292929;
    border: 1px solid #3c3c3c;
    border-radius: 8px;
    color: #e0e0e0;
    font-size: 15px;
    resize: vertical;
    transition: all 0.3s ease;
}

.disputes-show-textarea:focus {
    outline: none;
    border-color: #bb86fc;
    box-shadow: 0 0 0 2px rgba(187, 134, 252, 0.1);
}

.disputes-show-submit-btn {
    background-color: #bb86fc;
    color: #121212;
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
    display: block;
    width: 200px;
    margin: 0 auto;
}

.disputes-show-submit-btn:hover {
    background-color: #96c;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(187, 134, 252, 0.4);
}

.disputes-show-resolved-message {
    background-color: #292929;
    border-left: 4px solid #cf6679;
    color: #e0e0e0;
    padding: 15px;
    border-radius: 6px;
    margin-top: 20px;
    text-align: center;
}

/* Scrollbar styling for message list */
.disputes-show-messages-list::-webkit-scrollbar {
    width: 8px;
}

.disputes-show-messages-list::-webkit-scrollbar-track {
    background: #1e1e1e;
    border-radius: 4px;
}

.disputes-show-messages-list::-webkit-scrollbar-thumb {
    background-color: #bb86fc;
    border-radius: 4px;
    border: 2px solid #1e1e1e;
}

.disputes-show-messages-list::-webkit-scrollbar-thumb:hover {
    background-color: #96c;
}
</style>

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
                                minlength="1" 
                                maxlength="1000"></textarea>
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
