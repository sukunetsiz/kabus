@extends('layouts.app')

@section('content')

<div class="admin-support-index-container">
    @if($requests->isEmpty())
        <div class="admin-support-index-empty">
            No support requests found.
        </div>
    @else
        <div class="admin-support-index-card">
            <div class="admin-support-index-table-container">
                <table class="admin-support-index-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Last Update</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                            <tr>
                                <td>
                                    <span class="admin-support-index-username">{{ $request->user->username }}</span>
                                </td>
                                <td>
                                    <span class="admin-support-index-title-text">{{ $request->title }}</span>
                                </td>
                                <td>
                                    <span class="admin-support-index-status admin-support-index-status-{{ $request->status }}">
                                        @if($request->status === 'open')
                                           Open
                                        @elseif($request->status === 'in_progress')
                                           In Progress
                                        @else
                                           Closed
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <span class="admin-support-index-time">{{ $request->created_at->diffForHumans() }}</span>
                                </td>
                                <td>
                                    <span class="admin-support-index-time">
                                        {{ $request->latestMessage ? $request->latestMessage->created_at->diffForHumans() : 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.support.show', $request->ticket_id) }}" 
                                       class="admin-support-index-action-btn">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-support-index-pagination-container">
            {{ $requests->links() }}
        </div>
    @endif
</div>
@endsection
