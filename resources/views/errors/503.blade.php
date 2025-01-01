@extends('layouts.error')

@section('content')
    <div class="error-container">
        <div class="error-code">503</div>
        <div class="error-message">Service Unavailable</div>
        <div class="error-description">
            Our site is currently under maintenance or experiencing high load. We'll be back shortly. Thank you for your patience.
        </div>
        <a href="{{ url('/') }}" class="home-button">Return to {{ config('app.name') }}</a>
    </div>
@endsection