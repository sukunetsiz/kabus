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
                        <span>Used a Reference Code?</span>
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

        <div class="references-index-card">
            <h2>Add Vendor Reference IDs</h2>
            <div class="references-index-section">
                <p class="references-index-text">Enter a vendor's reference ID to add to your private shops list:</p>
        
               <form action="{{ route('references.store') }}" method="POST" class="references-index-form">
                   @csrf
                   <div class="references-index-input-group">
                       <input type="text" name="vendor_reference_id" placeholder="Vendor Reference ID" required minlength="12" maxlength="20" class="references-index-input">
                   </div>
                   <button type="submit" class="references-index-button">Add Vendor</button>
               </form>
        
               <div class="references-index-vendor-note">
                   <p>Note: Only vendor reference IDs can be added. Regular user IDs will not be accepted.</p>
               </div>
        
               @if(isset($privateShops) && $privateShops->count() > 0)
                   <h3>Your Saved Vendor References</h3>
                   <div class="references-index-vendor-list">
                       @foreach($privateShops as $shop)
                           <div class="references-index-vendor-item">
                               <div class="references-index-vendor-info">
                                   <strong class="references-index-vendor-name">{{ $shop->vendor_username }}</strong>
                                   <small class="references-index-vendor-id">{{ $shop->vendor_reference_id }}</small>
                               </div>
                               <form action="{{ route('references.remove', $shop->id) }}" method="POST">
                                   @csrf
                                   @method('DELETE')
                                   <button type="submit" class="references-index-remove-button">Remove</button>
                               </form>
                           </div>
                       @endforeach
                   </div>
               @else
                   <div class="references-index-vendor-empty">
                       <p>You haven't added any vendor reference IDs yet.</p>
                   </div>
               @endif
           </div>
        </div>
    </div>
</div>
@endsection
