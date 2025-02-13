@extends('layouts.app')

@section('content')
<div class="container">
    <div class="messages-rate-limit-wrapper">
        <div class="messages-rate-limit-card">
            <div class="messages-rate-limit-icon">
            </div>
            <h2 class="messages-rate-limit-title">Rate Limit Exceeded</h2>
            <div class="messages-rate-limit-alert">
                <h4 class="messages-rate-limit-alert-heading">Too Many Messages</h4>
                <p class="messages-rate-limit-message">For security reasons, we have temporarily limited your message sending frequency.</p>
                <div class="messages-rate-limit-divider"></div>
                <p class="messages-rate-limit-submessage">Please wait a while before sending another message.</p>
            </div>
            <div class="messages-rate-limit-action">
                <a href="{{ route('messages.index') }}" class="messages-rate-limit-button">
                    Return to Messages
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
