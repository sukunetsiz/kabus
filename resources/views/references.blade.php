@extends('layouts.app')
@section('content')
<div class="container references-container">
    <div class="references-row">
        <div class="references-col references-col-center">
            <div class="references-card references-card-id">
                <h2 class="references-card-header">Your Reference Number</h2>
                <div class="references-card-body references-card-body-id">
                    <p class="text-center">Your Unique Reference ID</p>
                    <div class="reference-id">
                        <strong>{{ $referenceId }}</strong>
                    </div>
                    <p class="text-center">Please share this number only with trusted people and not with everyone.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
