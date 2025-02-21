@extends('layouts.app')

@section('content')

<div class="support-index-container">
    <div class="support-index-header">
        <span class="support-index-badge">Support Requests</span>
        <a href="{{ route('support.create') }}" class="support-index-new-btn">
            New Support Request
        </a>
    </div>

    @if($requests->isEmpty())
        <div class="support-index-empty text-center">
            You haven't created any support requests yet.
        </div>
    @else
        <div class="support-index-table-container">
            <table class="support-index-table">
                <thead>
                    <tr>
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
                                <span class="support-index-title-text">{{ $request->title }}</span>
                            </td>
                            <td>
                                <span class="support-index-status support-index-status-{{ $request->status }}">
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
                                <span class="support-index-time">{{ $request->created_at->format('Y-m-d / H:i') }}</span>
                            </td>
                            <td>
                                <span class="support-index-time">
                                    {{ $request->latestMessage ? $request->latestMessage->created_at->format('Y-m-d / H:i') : 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('support.show', $request->ticket_id) }}" 
                                   class="support-index-action-btn">
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="support-index-pagination">
            {{ $requests->links() }}
        </div>
    @endif
</div>
@endsection
