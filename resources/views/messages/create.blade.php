@extends('layouts.app')

@section('content')
<div class="messages-create-container">    
    <div class="messages-create-wrapper">
        <div class="messages-create-card">
            <h2 class="messages-create-title">Start New Conversation</h2>
            <form action="{{ route('messages.start') }}" method="POST" class="messages-create-form">
                @csrf
                    <div class="messages-create-form-group">
                        <label for="username" class="messages-create-label">Username</label>
                        <input 
                            type="text" 
                            name="username" 
                            id="username" 
                            class="messages-create-input" 
                            required 
                            maxlength="16"
                            placeholder="Enter recipient's username" 
                            value="{{ old('username', $username ?? '') }}"
                        >
                    </div>
                    <div class="messages-create-form-group">
                        <label for="content" class="messages-create-label">Message</label>
                        <textarea 
                            name="content" 
                            id="content" 
                            class="messages-create-textarea" 
                            required 
                            minlength="4"
                            maxlength="1600"
                            placeholder="Type your message here..."
                        >{{ old('content') }}</textarea>
                    </div>
                    <div class="messages-create-actions">
                        <button type="submit" class="messages-create-submit">Start Conversation</button>
                    </div>
                </form>
        </div>
    </div>
</div>

@endsection
