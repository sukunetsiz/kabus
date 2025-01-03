@extends('layouts.app')

@section('content')
<div class="become-vendor-payment-container">
    <div class="become-vendor-payment-card">
        @if(isset($error))
            <div class="become-vendor-payment-alert become-vendor-payment-alert-danger text-center">
                <h2>Error</h2>
                <p>{{ $error }}</p>
            </div>
        @elseif(isset($alreadyVendor) && $alreadyVendor)
            <div class="become-vendor-payment-alert become-vendor-payment-alert-info text-center">
                <h2>You Are Already a Vendor</h2>
                <p>Your account already has vendor privileges. No additional payment is needed.</p>
            </div>
        @elseif(isset($vendorPayment))
            <div class="become-vendor-payment-info">
                <div class="become-vendor-payment-details text-center">
                    <p><strong>Address:</strong> <span class="become-vendor-payment-address">{{ $vendorPayment->address }}</span></p>
                    <p><strong>Required Amount:</strong> <span class="become-vendor-payment-amount">{{ config('monero.vendor_payment_required_amount') }} XMR</span></p>
                    <p><strong>Total Received:</strong> <span class="become-vendor-payment-received">{{ number_format($vendorPayment->total_received, 12) }} XMR</span></p>
                    <p><strong>Status:</strong> 
                        @if($vendorPayment->total_received >= config('monero.vendor_payment_required_amount'))
                            <span class="become-vendor-payment-status become-vendor-payment-status-success">Payment Successful - You are now a Vendor!</span>
                        @elseif($vendorPayment->total_received > 0)
                            <span class="become-vendor-payment-status become-vendor-payment-status-warning">Insufficient Amount</span>
                        @else
                            <span class="become-vendor-payment-status become-vendor-payment-status-info">Awaiting Payment</span>
                        @endif
                    </p>
                </div>
            </div>
            @if($qrCodeDataUri)
                <div class="become-vendor-payment-qr">
                    <img src="{{ $qrCodeDataUri }}" alt="Monero Address QR Code" class="become-vendor-payment-qr-image">
                </div>
            @endif
            <div class="become-vendor-payment-refresh">
                <a href="{{ route('become.payment') }}" class="become-vendor-payment-btn">Refresh to check for new transactions</a>
            </div>
        @else
            <p class="become-vendor-payment-error">Error occurred while creating Monero payment address. Please try again later.</p>
        @endif
    </div>
</div>

@endsection