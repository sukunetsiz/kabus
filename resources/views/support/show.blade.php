@extends('layouts.app')

@section('content')

<div class="support-show-container">
    <div class="support-show-card">
        <div class="support-show-header">
            <div class="support-show-title-group">
                <h1 class="support-show-title">{{ $supportRequest->title }}</h1>
                <div class="support-show-meta">
                    <span>Created at {{ $supportRequest->created_at->format('Y-m-d / H:i') }}</span>
                    <span class="support-show-status 
                        @if($supportRequest->status === 'open') support-show-status-open
                        @elseif($supportRequest->status === 'in_progress') support-show-status-in_progress
                        @else support-show-status-closed @endif">
                        @if($supportRequest->status === 'open')
                            Open
                        @elseif($supportRequest->status === 'in_progress')
                            In Progress
                        @else
                            Closed
                        @endif
                    </span>
                </div>
            </div>
            <a href="{{ route('support.index') }}" class="support-show-back-btn">
                Back to Requests
            </a>
        </div>

        <div class="support-show-messages">
            @foreach($messages as $message)
                <div class="support-show-message @if($message->is_admin_reply) admin-reply @endif">
                    <div class="support-show-message-header">
                        <div>
                            <a href="{{ route('dashboard', $message->user->username) }}" class="support-show-message-user">
                                {{ $message->user->username }}
                            </a>
                            @if($message->is_admin_reply)
                                <span class="support-show-admin-badge">Admin</span>
                            @endif
                        </div>
                        <div class="support-show-message-time">
                            {{ $message->created_at->format('Y-m-d / H:i') }}
                        </div>
                    </div>
                    <div class="support-show-message-content">{{ $message->formatted_message }}</div>
                </div>
            @endforeach
        </div>

        @if($supportRequest->status !== 'closed')
            <div class="support-show-reply-section">
                <form action="{{ route('support.reply', $supportRequest->ticket_id) }}" method="POST">
                    @csrf
                    <div class="support-show-form-group">
                        <label for="message" class="support-show-label">Your Reply</label>
                        <textarea name="message" id="message" required rows="4"
                            class="support-show-textarea"
                            placeholder="Write your message here"
                            minlength="8" maxlength="4000">{{ old('message') }}</textarea>
                    </div>

                    <div class="support-show-captcha-container">
                        <span class="support-show-captcha-label">CAPTCHA</span>
                        <img class="support-show-captcha-image" src="{{ new Mobicms\Captcha\Image($captchaCode) }}" alt="CAPTCHA">
                        <input type="text" name="captcha" id="captcha" required
                            class="support-show-captcha-input"
                            minlength="2" maxlength="8">
                    </div>

                    <div class="support-show-submit">
                        <button type="submit" class="support-show-submit-btn">
                            Send Reply
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="support-show-closed-message">
                This support request has been closed. If you need further assistance, please create a new support request.
            </div>
        @endif
    </div>
</div>
@endsection
