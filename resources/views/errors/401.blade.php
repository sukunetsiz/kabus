@extends('layouts.error')

@section('content')
    <div class="error-container">
        <div class="error-code">401</div>
        <div class="error-message">Hold Up! Who Goes There?</div>
        <div class="error-description">
            Looks like you're trying to sneak in without showing your digital ID! It's like trying to enter a spy movie without knowing the secret handshake. Maybe try logging in first? Our security guard is very particular about these things.
        </div>
        <a href="{{ route('login') }}" class="home-button">Return to {{ config('app.name') }}</a>
    </div>
@endsection
