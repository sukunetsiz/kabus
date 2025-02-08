@extends('layouts.app')

@section('content')
<div class="become-vendor-index-container">
    <div class="become-vendor-index-card">
        <h1 class="become-vendor-index-title">Become a Vendor</h1>
        
        <p class="become-vendor-index-text">You can also sell on {{ config('app.name') }}! All you need to do is pay the vendor fee and inform us about the products you will sell. You can contact us by opening a support ticket or sending a direct message to administrator.</p>
        
        <p class="become-vendor-index-text">Before making this purchase, make sure you have thoroughly read <a href="{{ route('rules') }}" class="become-vendor-index-link">{{ config('app.name') }}'s rules</a>. We have zero tolerance for prohibited products. The purchase process is instant and irreversible, no refunds will be made. If you accept these terms and site rules, welcome to {{ config('app.name') }}!</p>
        
        @if(!$hasPgpVerified)
            <div class="become-vendor-index-highlight" role="alert">
                <img src="{{ asset('images/information.png') }}" alt="Information" class="become-vendor-index-info-icon">
                <div class="become-vendor-index-highlight-content">
                    <h4 class="become-vendor-index-highlight-heading">PGP Verification Required!</h4>
                    <p class="become-vendor-index-highlight-text">For security reasons, you must verify your PGP key before becoming a vendor. This is a mandatory requirement to ensure secure communication with your customers.</p>
                    <hr class="become-vendor-index-divider">
                    <p class="become-vendor-index-highlight-text become-vendor-index-mb-0">Please visit your Account page to set up and verify your PGP key first.</p>
                </div>
            </div>
        @endif
        
        @if(!$hasMoneroAddress)
            <div class="become-vendor-index-highlight" role="alert">
                <img src="{{ asset('images/information.png') }}" alt="Information" class="become-vendor-index-info-icon">
                <div class="become-vendor-index-highlight-content">
                    <h4 class="become-vendor-index-highlight-heading">Monero Return Address Required!</h4>
                    <p class="become-vendor-index-highlight-text">You must add at least one Monero return address before becoming a vendor. This is required to ensure secure and reliable payment processing.</p>
                    <hr class="become-vendor-index-divider">
                    <p class="become-vendor-index-highlight-text become-vendor-index-mb-0">Please visit your Addresses page to add a Monero return address first.</p>
                </div>
            </div>
        @endif
        
        <a href="{{ route('become.payment') }}" class="become-vendor-index-btn {{ (!$hasPgpVerified || !$hasMoneroAddress) ? 'disabled' : '' }}">Continue to Payment</a>
    </div>
</div>
@endsection
