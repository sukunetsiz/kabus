@extends('layouts.app')

@section('content')
<div class="admin-vendor-application-detail">
    <h1>Review Vendor Application</h1>

    <div class="application-info">
        <div class="user-details">
            <h2>Applicant Details</h2>
            <p><strong>Username:</strong> {{ $application->user->username }}</p>
            <p><strong>Submitted:</strong> {{ $application->application_submitted_at->format('Y-m-d H:i:s') }}</p>
            <p><strong>Payment Amount:</strong> {{ $application->total_received }} XMR</p>
        </div>

        <div class="application-text">
            <h2>Application Text</h2>
            <pre>{{ $application->application_text }}</pre>
        </div>

        @if($application->application_images)
            <div class="product-images">
                <h2>Product Images</h2>
                <div class="image-grid">
                    @foreach(json_decode($application->application_images) as $image)
                        <div class="image-container">
                            <img src="{{ route('admin.vendor-applications.image', ['filename' => $image]) }}" alt="Product Image">
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($application->application_status === 'waiting')
            <div class="admin-actions">
                <h2>Make Decision</h2>
                <div class="action-buttons">
                    <form action="{{ route('admin.vendor-applications.accept', $application->id) }}" method="POST" class="inline-form">
                        @csrf
                        <button type="submit" class="accept-btn">Accept Application</button>
                    </form>

                    <form action="{{ route('admin.vendor-applications.deny', $application->id) }}" method="POST" class="inline-form">
                        @csrf
                        <button type="submit" class="deny-btn">Deny Application</button>
                    </form>
                </div>
            </div>
        @else
            <div class="application-status">
                <h2>Status</h2>
                <p>
                    This application has been 
                    <strong>
                        {{ $application->application_status === 'accepted' ? 'ACCEPTED' : 'DENIED' }}
                    </strong>
                    on {{ $application->admin_response_at->format('Y-m-d H:i:s') }}
                </p>
            </div>
        @endif
    </div>

    <div class="back-link">
        <a href="{{ route('admin.vendor-applications.index') }}">Back to Applications List</a>
    </div>
</div>
@endsection