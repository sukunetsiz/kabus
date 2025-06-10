@extends('layouts.app')

@section('content')
<div class="messages-show-container">    
    <div class="messages-show-card">
        <div class="messages-show-messages" id="messageContainer">
            @foreach($messages as $message)
                <div class="messages-show-message {{ $message->sender_id == Auth::id() ? 'messages-show-sent' : 'messages-show-received' }}">
                    <div class="messages-show-bubble">
                        <p class="messages-show-text">{{ $message->content }}</p>
                    </div>
                    <div class="messages-show-meta">
                        <a href="{{ route('dashboard', $message->sender->username) }}" class="messages-show-username">
                            {{ $message->sender->username }}
                        </a>
                        <span class="messages-show-time">{{ $message->created_at->format('Y-m-d / H:i') }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="messages-show-pagination">
            {{ $messages->links() }}
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($conversation->hasReachedMessageLimit())
            <div class="alert alert-error">
                Message limit of 40 reached for this conversation. Please delete this conversation and start a new one with the user.
            </div>
        @else
            <form action="{{ route('messages.store', $conversation) }}" method="POST" class="messages-show-form">
                @csrf
                <div class="messages-show-form-group">
                    <textarea 
                        name="content" 
                        class="messages-show-input" 
                        placeholder="Type your message here..." 
                        required 
                        minlength="4"
                        maxlength="1600"
                    ></textarea>
                </div>
                <button type="submit" class="messages-show-submit">
                    Send Message
                </button>
            </form>
        @endif
    </div>
</div>

@endsection
