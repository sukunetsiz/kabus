@extends('layouts.error')

@section('content')
    <div class="error-container">
        <div class="error-code">503</div>
        <div class="error-message">Spa Day for Our Servers!</div>
        <div class="error-description">
            Our servers are getting their well-deserved spa treatment (aka maintenance). They're enjoying a deep tissue defrag, a memory cache facial, and a bandwidth massage. We'll be back before you can say "Have you tried turning it off and on again?"
        </div>
        <a href="{{ route('login') }}" class="home-button">Return to {{ config('app.name') }}</a>
    </div>
@endsection
