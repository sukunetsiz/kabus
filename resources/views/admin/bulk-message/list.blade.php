@extends('layouts.app')

@section('content')

<div class="bulk-message-list-container">
    <div class="bulk-message-list-card">
        <div class="bulk-message-list-header">
            <h1 class="bulk-message-list-title">Sent Bulk Messages</h1>
            <a href="{{ route('admin.bulk-message.create') }}" class="bulk-message-list-new-btn">
                Send New Message
            </a>
        </div>

        @if($notifications->isEmpty())
            <div class="bulk-message-list-empty">
                <p>No bulk messages have been sent yet.</p>
            </div>
        @else
            <div class="bulk-message-list-table-container">
                <table class="bulk-message-list-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Target Group</th>
                            <th>Sent Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($notifications as $notification)
                            <tr>
                                <td>
                                    <div class="bulk-message-list-title-text">
                                        {{ $notification->title }}
                                    </div>
                                </td>
                                <td>
                                    <div class="bulk-message-list-target">
                                        @if($notification->target_role)
                                            {{ $notification->translated_role }}
                                        @else
                                            All Users
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="bulk-message-list-date">
                                        {{ $notification->created_at->format('d.m.Y H:i') }}
                                    </div>
                                </td>
                                <td>
                                    <form action="{{ route('admin.bulk-message.delete', $notification) }}" 
                                          method="POST" 
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bulk-message-list-delete-btn">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
