@extends('layouts.app')

@section('content')
<div class="container become-vendor-container">
    <div class="become-vendor-card">
        <h1 class="become-vendor-title">Become a Vendor</h1>
        
        <p class="become-vendor-text">You can also sell on {{ config('app.name') }}! All you need to do is pay the vendor fee and inform us about the products you will sell. You can contact us by opening a support ticket or sending a direct message to administrator.</p>
        
        <p class="become-vendor-text">Before making this purchase, make sure you have thoroughly read <a href="{{ route('rules') }}" class="become-vendor-link">{{ config('app.name') }}'s rules</a>. We have zero tolerance for prohibited products. The purchase process is instant and irreversible, no refunds will be made. If you accept these terms and site rules, welcome to {{ config('app.name') }}!</p>

        @if(!$hasPgpVerified)
            <div class="alert alert-warning become-vendor-warning" role="alert">
                <h4 class="alert-heading">PGP Verification Required!</h4>
                <p>For security reasons, you must verify your PGP key before becoming a vendor. This is a mandatory requirement to ensure secure communication with your customers.</p>
                <hr>
                <p class="mb-0">Please visit your Account page to set up and verify your PGP key first.</p>
            </div>
        @endif

        @if(!$hasMoneroAddress)
            <div class="alert alert-warning become-vendor-warning" role="alert">
                <h4 class="alert-heading">Monero Return Address Required!</h4>
                <p>You must add at least one Monero return address before becoming a vendor. This is required to ensure secure and reliable payment processing.</p>
                <hr>
                <p class="mb-0">Please visit your Addresses page to add a Monero return address first.</p>
            </div>
        @endif

        @if($hasPgpVerified && $hasMoneroAddress)
            <a href="{{ route('become.payment') }}" class="btn btn-primary become-vendor-btn">Continue to Payment</a>
        @endif
    </div>
</div>

@endsection
