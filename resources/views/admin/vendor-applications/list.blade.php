@extends('layouts.app')

@section('content')

<div class="vendor-applications-index-container">
    <div class="vendor-applications-index-card">
        <h1 class="vendor-applications-index-title">Vendor Applications</h1>

        @if($applications->isEmpty())
            <div class="vendor-applications-index-empty">
                <p>No pending vendor applications</p>
            </div>
        @else
            <div class="vendor-applications-index-table-container">
                <table class="vendor-applications-index-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Submitted At</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applications as $application)
                            <tr>
                                <td>{{ $application->user->username }}</td>
                                <td>{{ $application->application_submitted_at->format('Y-m-d / H:i') }}</td>
                                <td>
                                    <span class="vendor-applications-index-status vendor-applications-index-status-{{ $application->application_status }}">
                                        @if($application->application_status === 'waiting')
                                            Waiting for Review
                                        @elseif($application->application_status === 'accepted')
                                            Accepted
                                        @else
                                            Denied
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.vendor-applications.show', $application) }}"
                                       class="vendor-applications-index-btn">
                                        Review Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="vendor-applications-index-pagination">
                {{ $applications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
