@extends('layouts.app')

@section('content')

<div class="login-throttle-container">
    <h2 class="login-throttle-heading">
        Access Restricted
    </h2>
    
    <div class="login-throttle-alert">
        <p>Too many login attempts. Please try again in {{ $minutes }} minutes.</p>
        <hr class="login-throttle-hr">
        <p class="mb-0">Account security lock active</p>
    </div>

    <a href="{{ route('login') }}" class="login-throttle-button">
        Return to Login
    </a>
</div>
@endsection
