@extends('layouts.app')

@section('content')

<div class="become-vendor-index-container">
    <div class="become-vendor-index-card">
        <h1 class="become-vendor-index-title">Become a Vendor</h1>
        
        <p class="become-vendor-index-text">You can also sell on {{ config('app.name') }}! The process involves paying a vendor fee and submitting an application for review. Your application will be carefully reviewed by our administrators.</p>
        
        <p class="become-vendor-index-text">Before proceeding, make sure you have thoroughly read <a href="{{ route('rules') }}" class="become-vendor-index-link">{{ config('app.name') }}'s rules</a>. We have zero tolerance for prohibited products. The payment process is irreversible, and no refunds will be made. If you accept these terms and site rules, you may proceed with your application.</p>
        
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
        
@if(isset($vendorPayment))
    @if($vendorPayment->payment_completed)
        @if($vendorPayment->application_status === null)
            <div class="become-vendor-index-action-container">
                <p class="become-vendor-index-action-text">Your payment has been received. You can now submit your vendor application.</p>
                <a href="{{ route('become.vendor.application') }}" class="become-vendor-index-btn">Create Application</a>
            </div>
        @else
            <div class="become-vendor-index-status-container">
                <h3 class="become-vendor-index-status-title">Application Status: 
                    @if($vendorPayment->application_status === 'waiting')
                        <span class="become-vendor-index-status become-vendor-index-status-waiting">Waiting for Review</span>
                    @elseif($vendorPayment->application_status === 'accepted')
                        <span class="become-vendor-index-status become-vendor-index-status-accepted">Accepted - You are now a vendor!</span>
                    @else
                        <span class="become-vendor-index-status become-vendor-index-status-denied">Denied</span>
                    @endif
                </h3>
                @if($vendorPayment->application_status === 'waiting')
                    <p class="become-vendor-index-status-message">Your application is currently being reviewed by our administrators.</p>
                @elseif($vendorPayment->application_status === 'accepted')
                    <p class="become-vendor-index-status-message">Congratulations! Your application has been accepted. You can now access the vendor features.</p>
                @else
                    <p class="become-vendor-index-status-message">Unfortunately, your application has been denied. You cannot submit a new application at this time.</p>
                    @if($vendorPayment->refund_amount)
                        <div class="become-vendor-index-refund-info">
                            <p class="become-vendor-index-status-message">A refund of {{ $vendorPayment->refund_amount }} XMR has been sent to your return address.</p>
                        </div>
                    @endif
                @endif
            </div>
        @endif
    @else
        <a href="{{ route('become.payment') }}" class="become-vendor-index-btn {{ (!$hasPgpVerified || !$hasMoneroAddress) ? 'disabled' : '' }}">Continue to Payment</a>
    @endif
@else
    <a href="{{ route('become.payment') }}" class="become-vendor-index-btn {{ (!$hasPgpVerified || !$hasMoneroAddress) ? 'disabled' : '' }}">Continue to Payment</a>
@endif
    <p class="become-vendor-index-highlight-info">Established vendors can receive the vendor role for free by verifying their PGP key and opening a support ticket. You'll be asked to provide proof of prior vendor experience, such as profiles or feedback from other marketplaces. Verification time may vary.</p>
    <a href="{{ route('support.create') }}" class="become-vendor-index-btn">Apply as Established Vendor</a>
    </div>
</div>
@endsection
