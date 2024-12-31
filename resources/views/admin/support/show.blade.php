@extends('layouts.app')

@section('content')
<div class="admin-support-show-container">
    <div class="admin-support-show-card">
        <div class="admin-support-show-header">
            <div class="admin-support-show-title-group">
                <h1 class="admin-support-show-username">Support Request from {{ $supportRequest->user->username }}</h1>
                <div class="admin-support-show-subject">{{ $supportRequest->title }}</div>
                <div class="admin-support-show-meta">
                    <span>Created {{ $supportRequest->created_at->diffForHumans() }}</span>
                    <form action="{{ route('admin.support.status', $supportRequest->ticket_id) }}" method="POST" class="admin-support-show-status-form">
                        @csrf
                        @method('PUT')
                        <select name="status" onchange="this.form.submit()"
                            class="admin-support-show-status-select @if($supportRequest->status === 'open') status-open
                            @elseif($supportRequest->status === 'in_progress') status-progress
                            @else status-closed @endif">
                            <option value="open" @if($supportRequest->status === 'open') selected @endif>Open</option>
                            <option value="in_progress" @if($supportRequest->status === 'in_progress') selected @endif>In Progress</option>
                            <option value="closed" @if($supportRequest->status === 'closed') selected @endif>Closed</option>
                        </select>
                    </form>
                </div>
            </div>
            <a href="{{ route('admin.support.requests') }}" class="admin-support-show-back-btn">
                Return to Requests
            </a>
        </div>

        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="admin-support-show-messages">
            @foreach($messages as $message)
                <div class="admin-support-show-message @if($message->is_admin_reply) admin-reply @endif">
                    <div class="admin-support-show-message-header">
                        <div class="admin-support-show-message-user">
                            {{ $message->user->username }}
                            @if($message->is_admin_reply)
                                <span class="admin-support-show-admin-badge">Admin</span>
                            @endif
                        </div>
                        <div class="admin-support-show-message-time">
                            {{ $message->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <div class="admin-support-show-message-content">{{ $message->formatted_message }}</div>
                </div>
            @endforeach
        </div>

        @if($supportRequest->status !== 'closed')
            <div class="admin-support-show-reply-section">
                <form action="{{ route('admin.support.reply', $supportRequest->ticket_id) }}" method="POST">
                    @csrf
                    <div class="admin-support-show-form-group">
                        <label for="message" class="admin-support-show-label text-center">Admin Reply</label>
                        <textarea name="message" id="message" required rows="4"
                            class="admin-support-show-textarea @error('message') is-invalid @enderror"
                            placeholder="Write your reply here">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="admin-support-show-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="admin-support-show-submit">
                        <button type="submit" class="admin-support-show-submit-btn">
                            Send Reply
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="admin-support-show-closed-message">
                This request is closed. Please change the status to open or in progress to reply.
            </div>
        @endif
    </div>
</div>

@endsection
