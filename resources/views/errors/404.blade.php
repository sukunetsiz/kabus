@extends('layouts.error')

@section('content')
    <div class="error-container">
        <div class="error-code">404</div>
        <div class="error-message">Oops! Page Not Found</div>
        <div class="error-description">
            The page you're looking for might have been removed, renamed, or is temporarily unavailable. Don't worry, even the best explorers get lost sometimes.
        </div>
        <a href="{{ url('/') }}" class="home-button">Return to {{ config('app.name') }}</a>
    </div>
@endsection