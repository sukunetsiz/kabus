@extends('layouts.error')

@section('content')
    <div class="error-container">
        <div class="error-code">419</div>
        <div class="error-message">Whoops! Your Session Timed Out</div>
        <div class="error-description">
            Looks like you took a bit too long to decide - just like when you're staring at your food delivery app trying to pick dinner! Don't worry, we all need time to think, but this session got tired of waiting. How about we start fresh?
        </div>
        <a href="{{ route('login') }}" class="home-button">Return to {{ config('app.name') }}</a>
    </div>
@endsection
