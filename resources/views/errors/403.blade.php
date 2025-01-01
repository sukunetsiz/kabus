@extends('layouts.error')

@section('content')
    <div class="error-container">
        <div class="error-code">403</div>
        <div class="error-message">Whoa there! VIP Area Ahead</div>
        <div class="error-description">
            Looks like you're trying to sneak into the digital equivalent of a backstage area! Unfortunately, your name isn't on the guest list. If you think there's been a mix-up with the bouncer (our security system), please contact our VIP coordinator (site administrator).
        </div>
        <a href="{{ url('/') }}" class="home-button">Return to {{ config('app.name') }}</a>
    </div>
@endsection