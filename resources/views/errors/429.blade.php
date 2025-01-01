@extends('layouts.error')

@section('content')
    <div class="error-container">
        <div class="error-code">429</div>
        <div class="error-message">Whoa! Slow Down, Speed Racer!</div>
        <div class="error-description">
            You're clicking faster than a caffeinated squirrel on a sugar rush! Our servers need a moment to catch their breath. Why not take a sip of water, do a little stretch, or count to ten? We'll be ready for more of your enthusiasm in just a moment.
        </div>
        <a href="{{ url('/') }}" class="home-button">Return to {{ config('app.name') }}</a>
    </div>
@endsection