@extends('layouts.app')

@section('title', 'Kabus Market')

@section('content')
<div class="container references-container">
    <div class="references-row">
        <div class="references-col references-col-left">
            <div class="references-card references-card-stats">
                <h2 class="references-card-header">Reference Statistics</h2>
                <div class="references-card-body references-card-body-stats text-center">
                    <p>Number of people using your reference: <strong>{{ $referralCount }}</strong></p>
                    
                    @if($referredByUsers->count() > 0)
                        <h3>People Using Your Reference</h3>
                        <ul class="references-user-list">
                            @foreach($referredByUsers as $user)
                                <li>{{ $user->username }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
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
        <div class="references-col references-col-right">
            <div class="references-card references-card-referrals">
                <h2 class="references-card-header">Your References</h2>
                <div class="references-card-body references-card-body-referrals">
                    @if($referrals->count() > 0)
                        <ul class="references-referral-list">
                            @foreach($referrals as $referral)
                                <li>
                                    <span>-Reference Number- {{ $referral->referred_user_reference_id }}</span>
                                    <span>-User- {{ $referral->referredUser->username }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-center">You haven't added any references yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="references-row">
        <div class="references-col references-col-center">
            <div class="references-card references-card-add-referral">
                <h2 class="references-card-header">Add Reference</h2>
                <div class="references-card-body references-card-body-add">
                    <form method="POST" action="{{ route('references.add') }}">
                        @csrf
                        <div class="references-form-group text-center">
                            <label for="reference_id">Enter Reference Number</label>
                            <input type="text" class="references-form-control @error('reference_id') is-invalid @enderror" id="reference_id" name="reference_id" value="{{ old('reference_id') }}" required>
                            @error('reference_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-submit">Add Reference</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection