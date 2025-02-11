@extends('layouts.app')

@section('content')
<div class="admin-vendor-applications">
    <h1>Vendor Applications</h1>

    @if($applications->isEmpty())
        <p>No vendor applications to review.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Submitted</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($applications as $application)
                    <tr>
                        <td>{{ $application->user->username }}</td>
                        <td>{{ $application->application_submitted_at->format('Y-m-d H:i:s') }}</td>
                        <td>
                            @if($application->application_status === 'waiting')
                                Waiting for Review
                            @elseif($application->application_status === 'accepted')
                                Accepted
                            @else
                                Denied
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.vendor-applications.show', $application->id) }}">
                                Review Application
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $applications->links() }}
    @endif
</div>
@endsection