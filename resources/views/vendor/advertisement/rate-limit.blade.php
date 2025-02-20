@extends('layouts.app')

@section('content')

<div class="advertisement-rate-limit-container">
    <h1 class="advertisement-rate-limit-header">Advertisement Request Limit Reached</h1>
    
    <div class="advertisement-rate-limit-message">
        <p>You have reached the maximum number of advertisement payment requests for today.</p>
        <p>This measure ensures fair access to our advertising system for all vendors.</p>
        
        @if($cooldownEnds)
            <div class="advertisement-rate-limit-cooldown">
                Please try again after:<strong>{{ $cooldownEnds->format('Y-m-d / H:i') }}</strong>
            </div>
        @endif
    </div>

    <div class="advertisement-rate-limit-button">
        <a href="{{ route('vendor.my-products') }}">
            Return to My Products
        </a>
    </div>
</div>
@endsection

