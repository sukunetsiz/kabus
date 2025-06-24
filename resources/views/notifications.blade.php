@extends('layouts.app')
@section('content')

<div class="notifications-container">
    <div class="notifications-card">
        <div class="notifications-header">
            <h1 class="notifications-title">Your Notifications</h1>
        </div>
        @if($notifications->isEmpty())
            <div class="notifications-empty">
                <div class="notifications-empty-icon">📭</div>
                <p>You don't have any notifications yet.</p>
            </div>
        @else
            <div class="notifications-list">
                @foreach($notifications as $notification)
                    <div class="notifications-item">
                        <h3 class="notifications-item-title">
                            {{ $notification->title }}
                        </h3>
                        <p class="notifications-item-message">
                            {{ $notification->message }}
                        </p>
                        <div class="notifications-item-footer">
                            <div class="notifications-item-status">
                                @if($notification->pivot->read)
                                    <span class="notifications-read-badge">Read</span>
                                @else
                                    <form method="POST" action="{{ route('notifications.mark-read', ['notification' => $notification->id]) }}">
                                        @csrf
                                        <button type="submit" class="notifications-btn notifications-btn-read">Mark as Read</button>
                                    </form>
                                @endif
                                <span class="notifications-time">
                                    {{ $notification->created_at->format('d-m-Y / H:i') }}
                                </span>
                            </div>
                            <div class="notifications-actions">
                                <form method="POST" action="{{ route('notifications.destroy', ['notification' => $notification->id]) }}">
                                    @csrf
                                    <button type="submit" class="notifications-btn notifications-btn-delete">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="notifications-pagination">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
