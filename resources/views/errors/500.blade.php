@extends('layouts.error')

@section('content')
    <div class="error-container">
        <div class="error-code">500</div>
        <div class="error-message">Our Server is Having a Monday Moment</div>
        <div class="error-description">
            Even our mighty server sometimes needs a coffee break! Right now it's having what we call a "technical brain freeze" - you know, like when you eat ice cream too fast, but with code. Our tech team is already brewing a fresh pot of coffee and working on perking things up. Maybe try again in a bit when the server has had its caffeine fix?
        </div>
        <a href="{{ route('login') }}" class="home-button">Return to {{ config('app.name') }}</a>
    </div>
@endsection
