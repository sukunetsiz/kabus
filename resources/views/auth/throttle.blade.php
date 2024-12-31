@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card" style="padding: 30px; max-width: 600px; width: 100%; margin: 40px auto;">
        <h2 style="text-align: center; margin-bottom: 20px;">Too Many Attempts</h2>
        <div class="alert alert-error" role="alert" style="text-align: center;">
            <h4 class="alert-heading" style="margin-bottom: 15px;">Login Attempt Limit Exceeded</h4>
            <p>For security reasons, we have temporarily locked your account due to multiple failed login attempts.</p>
            <hr style="margin: 15px 0;">
            <p class="mb-0">Please try again in: {{ $minutes }} minutes</p>
        </div>
        <div style="text-align: center; margin-top: 25px;">
            <a href="{{ route('login') }}" class="btn btn-submit" style="width: auto; padding: 12px 30px;">Return to Login Page</a>
        </div>
    </div>
</div>
@endsection