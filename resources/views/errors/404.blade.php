@extends('layouts.error')

@section('content')
    <div class="error-container">
        <div class="error-code">404</div>
        <div class="error-message">Oops! Page Pulled a Houdini</div>
        <div class="error-description">
            This page has vanished like socks in a dryer! We've searched high and low, checked under the digital couch cushions, but it seems to have mastered the art of disappearing. Maybe it's on vacation with all those missing left socks?
        </div>
        <a href="{{ route('home') }}" class="home-button">Return to {{ config('app.name') }}</a>
    </div>
@endsection
