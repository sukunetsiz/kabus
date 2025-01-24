@extends('layouts.app')

@section('content')

<div class="notifications-index-container text-center">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="notifications-index-card">
        <div class="notifications-index-header">
            <h1 class="notifications-index-title">Your Notifications</h1>
        </div>

        @if($notifications->isEmpty())
            <div class="notifications-index-empty text-center">
                <p>You don't have any notifications yet.</p>
            </div>
        @else
            <div class="notifications-index-list">
                @foreach($notifications as $notification)
                    <div class="notifications-index-item">
                        <h3 class="notifications-index-item-title">
                            {{ $notification->title }}
                        </h3>
                        <p class="notifications-index-item-message">
                            {{ $notification->message }}
                        </p>
                        <div class="notifications-index-item-footer">
                            @if($notification->pivot->read)
                                <span class="notification-action notification-read">✅</span>
                            @else
                                <form method="POST" action="{{ route('notifications.mark-read', ['notification' => $notification->id]) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="notification-action notification-read">👁️</button>
                                </form>
                            @endif

                            <span class="notifications-index-item-time">
                                {{ $notification->created_at->format('d.m.Y H:i') }}
                            </span>

                            <form method="POST" action="{{ route('notifications.destroy', ['notification' => $notification->id]) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="notification-action notification-delete">❌</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection