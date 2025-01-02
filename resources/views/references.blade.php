@extends('layouts.app')

@section('content')
<div class="references-index-container">
    <div class="references-index-grid">
        <div class="references-index-card">
            <h2>Reference Information</h2>
            <div class="references-index-section">
                <p class="references-index-text">Your Unique Reference ID</p>
                <div class="references-index-highlight">
                    <strong>{{ $referenceId }}</strong>
                </div>
                <div class="references-index-ref-info">
                    <div class="references-index-ref-box">
                        <span>Used Reference Code</span>
                        <strong>{{ $usedReferenceCode ? 'Yes' : 'No' }}</strong>
                    </div>
                    @if($usedReferenceCode && $referrerUsername)
                    <div class="references-index-ref-box">
                        <span>Referred By</span>
                        <strong>{{ $referrerUsername }}</strong>
                    </div>
                    @endif
                </div>
                <p class="references-index-note">Please share this number only with trusted people and not with everyone.</p>
            </div>
        </div>
        <div class="references-index-card">
            <h2>Users Who Used Your Reference</h2>
            <div class="references-index-section">
                @if($referrals->count() > 0)
                    <p class="references-index-text">The following users have used your reference code:</p>
                    <div class="references-index-referral-list">
                        @foreach($referrals as $referral)
                            <div class="references-index-referral-item">
                                <strong>{{ $referral->username }}</strong>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="references-index-highlight">
                        <p class="references-index-text">No one has used your reference code yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
