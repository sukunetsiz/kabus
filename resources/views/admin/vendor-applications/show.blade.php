@extends('layouts.app')

@section('content')

<div class="vendor-applications-show-container">
    <div class="vendor-applications-show-card">
        <h1 class="vendor-applications-show-title">Review Vendor Application</h1>

        <div class="vendor-applications-show-section">
            <h2 class="vendor-applications-show-section-title">Applicant Details</h2>
            <p class="vendor-applications-show-detail-row"><strong>Username:</strong> {{ $application->user->username }}</p>
            <p class="vendor-applications-show-detail-row"><strong>Submitted:</strong> {{ $application->application_submitted_at->format('Y-m-d / H:i') }}</p>
            <p class="vendor-applications-show-detail-row"><strong>Payment Amount:</strong> {{ $application->total_received }} XMR</p>
        </div>

        <div class="vendor-applications-show-section">
            <h2 class="vendor-applications-show-section-title">Application Text</h2>
            <div class="vendor-applications-show-application-text">{{ $application->application_text }}</div>
        </div>

        @if($application->application_images)
            <div class="vendor-applications-show-section">
                <h2 class="vendor-applications-show-section-title">Product Images</h2>
                <div class="vendor-applications-show-image-grid">
                    @foreach(json_decode($application->application_images) as $image)
                        <div class="vendor-applications-show-image-container">
                            <img src="{{ route('admin.vendor-applications.show', ['application' => $application, 'image' => $image]) }}" alt="Product Image">
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($application->application_status === 'waiting')
            <div class="vendor-applications-show-section">
                <h2 class="vendor-applications-show-section-title">Make Decision</h2>
                <div class="vendor-applications-show-actions">
                    <form action="{{ route('admin.vendor-applications.accept', $application) }}" method="POST" class="inline-form">
                        @csrf
                        <button type="submit" class="vendor-applications-show-btn vendor-applications-show-btn-accept">Accept Application</button>
                    </form>

                    <form action="{{ route('admin.vendor-applications.deny', $application) }}" method="POST" class="inline-form">
                        @csrf
                        <button type="submit" class="vendor-applications-show-btn vendor-applications-show-btn-deny">Deny Application</button>
                    </form>
                </div>
            </div>
        @else
            <div class="vendor-applications-show-section">
                <h2 class="vendor-applications-show-section-title">Status</h2>
                <div class="vendor-applications-show-status">
                <p>
                    This application has been 
                    <strong>
                        {{ $application->application_status === 'accepted' ? 'ACCEPTED' : 'DENIED' }}
                    </strong>
                    on {{ $application->admin_response_at->format('Y-m-d / H:i') }}
                </p>
                </div>
            </div>

            @if($application->application_status === 'denied' && $application->refund_amount)
            <div class="vendor-applications-show-section">
                <h2 class="vendor-applications-show-section-title">Refund Details</h2>
                <div class="vendor-applications-show-status">
                    <p class="vendor-applications-show-detail-row">
                        <strong>Refund Amount:</strong> {{ $application->refund_amount }} XMR
                    </p>
                    <p class="vendor-applications-show-detail-row">
                        <strong>Refund Address:</strong> {{ $application->refund_address }}
                    </p>
                </div>
            </div>
            @endif
        @endif

        <div class="vendor-applications-show-back">
            <a href="{{ route('admin.vendor-applications.index') }}" class="vendor-applications-show-back-link">Back to Applications List</a>
        </div>
    </div>
</div>
@endsection
