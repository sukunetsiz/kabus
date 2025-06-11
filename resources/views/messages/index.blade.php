@extends('layouts.app')

@section('content')

<div class="messages-index-container">
    <div class="messages-index-card">
        @if(!Auth::user()->hasReachedConversationLimit())
            <a href="{{ route('messages.create') }}" class="messages-index-new-btn">Start New Conversation</a>
        @endif
        
        @if($conversations->isEmpty())
            <div class="messages-index-empty">You don't have any conversations yet.</div>
        @else
            <div class="messages-index-list">
                @foreach($conversations as $conversation)
                    <div class="messages-index-item">
                        <a href="{{ route('messages.show', $conversation) }}" class="messages-index-item-link">
                            <div class="messages-index-header">
                                <h5 class="messages-index-username">
                                    @if($conversation->user1->id == Auth::id())
                                        {{ $conversation->user2->username }}
                                    @else
                                        {{ $conversation->user1->username }}
                                    @endif
                                </h5>
                                <span class="messages-index-time">
                                    @if($conversation->last_message_at)
                                        {{ $conversation->last_message_at->format('Y-m-d / H:i') }}
                                    @else
                                        No messages yet
                                    @endif
                                </span>
                            </div>
                            <p class="messages-index-preview">
                                @if($conversation->messages->isNotEmpty())
                                    {{ Str::limit($conversation->messages->last()->content, 100) }}
                                @else
                                    No messages yet
                                @endif
                            </p>
                        </a>
                        <form action="{{ route('messages.destroy', $conversation) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="messages-index-delete" title="Delete conversation">×</button>
                        </form>
                    </div>
                @endforeach
            </div>
            <div class="messages-index-pagination">
                {{ $conversations->links('components.pagination') }}
            </div>
        @endif

        @if (Auth::user()->hasReachedConversationLimit())
            <div class="messages-index-limit-warning">
                Conversation limit of 16 reached. Please delete other conversations to create a new one.
            </div>
        @endif
    </div>
</div>
@endsection
