@extends('layouts.app')

@section('content')
<div class="messages-create-container">    
    <div class="messages-create-wrapper">
        <div class="messages-create-card">
            @if (Auth::user()->hasReachedConversationLimit())
                <div class="alert alert-warning">
                    Conversation limit of 16 reached. Please delete other conversations to create a new one.
                </div>
            @else
                <h2 class="messages-create-title">Start New Conversation</h2>
                <form action="{{ route('messages.start') }}" method="POST" class="messages-create-form">
                    @csrf
                    <div class="messages-create-form-group">
                        <label for="username" class="messages-create-label">Username</label>
                        <input 
                            type="text" 
                            name="username" 
                            id="username" 
                            class="messages-create-input @error('username') messages-create-input-error @enderror" 
                            required 
                            placeholder="Enter recipient's username" 
                            value="{{ old('username') }}"
                        >
                        @error('username')
                            <div class="messages-create-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="messages-create-form-group">
                        <label for="content" class="messages-create-label">Message</label>
                        <textarea 
                            name="content" 
                            id="content" 
                            class="messages-create-textarea @error('content') messages-create-input-error @enderror" 
                            required 
                            placeholder="Type your message here..."
                        >{{ old('content') }}</textarea>
                        @error('content')
                            <div class="messages-create-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="messages-create-actions">
                        <button type="submit" class="messages-create-submit">Start Conversation</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

@endsection
