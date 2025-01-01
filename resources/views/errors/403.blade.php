@extends('layouts.error')

@section('content')
    <div class="error-container">
        <div class="error-code">403</div>
        <div class="error-message">Access Forbidden</div>
        <div class="error-description">
            Sorry, you don't have permission to access this page. If you think this is an error, please contact the site administrator.
        </div>
        <a href="{{ url('/') }}" class="home-button">Return to {{ config('app.name') }}</a>
    </div>
@endsection