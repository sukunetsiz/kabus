@extends('layouts.app')

@section('content')
<div>
    <div>
        <h1>Dispute Management</h1>
        <div>
            <a href="{{ route('admin.disputes.index') }}">
                Return to Disputes
            </a>
        </div>
    </div>

    {{-- Dispute Info --}}
    <div>
        <div>
            <div>
                <h2>Dispute Information</h2>
                <div>
                    Status: <span>{{ $dispute->getFormattedStatus() }}</span>
                </div>
            </div>
            
            <div>
                <div>
                    <div>Order ID</div>
                    <div>{{ substr($dispute->order->id, 0, 8) }}</div>
                </div>
                <div>
                    <div>Dispute Created</div>
                    <div>{{ $dispute->created_at->format('Y-m-d / H:i') }}</div>
                </div>
                <div>
                    <div>Buyer</div>
                    <div>{{ $dispute->order->user->username }}</div>
                </div>
                <div>
                    <div>Vendor</div>
                    <div>{{ $dispute->order->vendor->username }}</div>
                </div>
                <div>
                    <div>Reason for Dispute</div>
                    <div>{{ $dispute->reason }}</div>
                </div>
                @if($dispute->resolved_at)
                    <div>
                        <div>Resolved On</div>
                        <div>{{ $dispute->resolved_at->format('Y-m-d / H:i') }}</div>
                    </div>
                    <div>
                        <div>Resolved By</div>
                        <div>{{ $dispute->resolver->username }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Resolution Actions (Only for active disputes) --}}
    @if($dispute->status === \App\Models\Dispute::STATUS_ACTIVE)
        <div>
            <div>
                <h2>Resolve Dispute</h2>
                <div>
                    <p>Please select a resolution for this dispute. This action cannot be undone.</p>
                    <ul>
                        <li><strong>Vendor Prevails:</strong> Order will be marked as completed.</li>
                        <li><strong>Buyer Prevails:</strong> Order will be marked as cancelled.</li>
                    </ul>
                </div>
                
                <div>
                    <form action="{{ route('admin.disputes.vendor-prevails', $dispute->id) }}" method="POST">
                        @csrf
                        <div>
                            <label for="vendor-message">Resolution Message (Optional)</label>
                            <textarea 
                                id="vendor-message" 
                                name="message" 
                                placeholder="Explain why the vendor prevails..."></textarea>
                        </div>
                        <button type="submit">
                            Resolve: Vendor Prevails
                        </button>
                    </form>
                    
                    <form action="{{ route('admin.disputes.buyer-prevails', $dispute->id) }}" method="POST">
                        @csrf
                        <div>
                            <label for="buyer-message">Resolution Message (Optional)</label>
                            <textarea 
                                id="buyer-message" 
                                name="message" 
                                placeholder="Explain why the buyer prevails..."></textarea>
                        </div>
                        <button type="submit">
                            Resolve: Buyer Prevails
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Dispute Chat --}}
    <div>
        <div>
            <h2>Dispute Messages</h2>
            
            <div>
                @if($dispute->messages->isEmpty())
                    <div>
                        No messages in this dispute yet.
                    </div>
                @else
                    @foreach($dispute->messages as $message)
                        <div>
                            <div>
                                <div>
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
                                <div>
                                    {{ $message->created_at->format('Y-m-d / H:i') }}
                                </div>
                            </div>
                            <div>
                                {{ $message->message }}
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            
            {{-- Message Form (Only for active disputes) --}}
            @if($dispute->status === \App\Models\Dispute::STATUS_ACTIVE)
                <div>
                    <form action="{{ route('disputes.add-message', $dispute->id) }}" method="POST">
                        @csrf
                        <div>
                            <label for="message">Add a Message</label>
                            <textarea 
                                id="message" 
                                name="message" 
                                placeholder="Type your message here..." 
                                required 
                                minlength="1" 
                                maxlength="1000"></textarea>
                        </div>
                        <div>
                            <button type="submit">Send Message</button>
                        </div>
                    </form>
                </div>
            @else
                <div>
                    <p>This dispute has been resolved. No new messages can be added.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
