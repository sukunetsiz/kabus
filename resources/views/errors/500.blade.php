@extends('layouts.error')

@section('content')
    <div class="error-container">
        <div class="error-code">500</div>
        <div class="error-message">Internal Server Error</div>
        <div class="error-description">
            Oops! Something went wrong on our end. We're working to fix the problem. Please try again later or contact our support team if the issue persists.
        </div>
        <a href="{{ url('/') }}" class="home-button">Return to {{ config('app.name') }}</a>
    </div>
@endsection