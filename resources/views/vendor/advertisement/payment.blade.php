@extends('layouts.app')

@section('content')

<div class="advertisement-payment-container">
    <div class="advertisement-payment-content">
        <div style="text-align: center;">
            <h1 class="advertisement-payment-title">Advertisement Payment</h1>
        </div>

        <div class="advertisement-payment-grid">
            <!-- Payment Details -->
            <div class="advertisement-payment-card">
                <h2 class="advertisement-payment-subtitle">Payment Details</h2>
                <div class="advertisement-payment-details">
                    <div class="advertisement-payment-row">
                        <span class="advertisement-payment-label">Product:</span>
                        <span class="advertisement-payment-value">{{ $advertisement->product->name }}</span>
                    </div>
                    
                    <div class="advertisement-payment-row">
                        <span class="advertisement-payment-label">Selected Slot:</span>
                        <span class="advertisement-payment-value">Slot {{ $advertisement->slot_number }}</span>
                    </div>
                    
                    <div class="advertisement-payment-row">
                        <span class="advertisement-payment-label">Duration:</span>
                        <span class="advertisement-payment-value">{{ $advertisement->duration_days }} {{ Str::plural('day', $advertisement->duration_days) }}</span>
                    </div>
                    
                    <div class="advertisement-payment-row">
                        <span class="advertisement-payment-label">Required Amount:</span>
                        <span class="advertisement-payment-value advertisement-payment-amount">ɱ{{ number_format($advertisement->required_amount, 12) }} XMR</span>
                    </div>

                    <div class="advertisement-payment-row">
                        <span class="advertisement-payment-label">Minimum Payment:</span>
                        <div class="advertisement-payment-value-group">
                            <span class="advertisement-payment-amount">ɱ{{ number_format($advertisement->required_amount * config('monero.advertisement_minimum_payment_percentage'), 12) }} XMR</span>
                        </div>
                    </div>

                    <div class="advertisement-payment-row">
                        <span class="advertisement-payment-label">Payment Status:</span>
                        <div class="advertisement-payment-status-wrapper">
                            @if($advertisement->payment_completed)
                                <span class="advertisement-payment-status advertisement-payment-status-completed">
                                    Payment Completed
                                </span>
                            @elseif($advertisement->total_received > 0 && $advertisement->total_received < $advertisement->required_amount)
                                <span class="advertisement-payment-status advertisement-payment-status-insufficient">
                                    Insufficient Amount
                                </span>
                            @else
                                <span class="advertisement-payment-status advertisement-payment-status-awaiting">
                                    Awaiting Payment
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($advertisement->total_received > 0 && !$advertisement->payment_completed)
                        <div class="advertisement-payment-row">
                            <span class="advertisement-payment-label">Amount Received:</span>
                            <div class="advertisement-payment-value-group">
                                <span class="advertisement-payment-amount">ɱ{{ number_format($advertisement->total_received, 12) }} XMR</span>
                                <span class="advertisement-payment-remaining">
                                    Remaining: ɱ{{ number_format($advertisement->required_amount - $advertisement->total_received, 12) }} XMR
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="advertisement-payment-expiry">
                    <p>
                        Payment window expires: {{ $advertisement->expires_at->diffForHumans() }}
                    </p>
                </div>
            </div>

            <!-- QR Code and Payment Address -->
            <div class="advertisement-payment-card">
                @if($qrCode)
                <h2 class="advertisement-payment-subtitle">Scan QR Code</h2>
                    <div class="advertisement-payment-qr">
                        <img src="{{ $qrCode }}" alt="Payment QR Code" class="advertisement-payment-qr-image">
                    </div>
                @endif

                <h2 class="advertisement-payment-subtitle" style="margin-top: 20px;">Payment Address</h2>
                <div class="advertisement-payment-address">
                    {{ $advertisement->payment_address }}
                </div>
            </div>

            @if(!$advertisement->payment_completed)
                <div class="advertisement-payment-refresh">
                    <a href="{{ route('vendor.advertisement.payment', $advertisement->payment_identifier) }}" class="advertisement-payment-refresh-btn">
                        Refresh to check for new transactions
                    </a>
                </div>
            @endif
        </div>

        @if($advertisement->payment_completed)
            <div class="advertisement-payment-success">
                <p class="advertisement-payment-message">
                    Your payment has been completed successfully. Your advertisement will be displayed in slot {{ $advertisement->slot_number }} 
                    for {{ $advertisement->duration_days }} {{ Str::plural('day', $advertisement->duration_days) }}.
                </p>
            </div>
        @else
            <div class="advertisement-payment-instructions">
                <p class="advertisement-payment-message">
                    Please send exactly ɱ{{ number_format($advertisement->required_amount, 12) }} XMR to the address above. 
                    The payment will be detected once confirmed on the blockchain.
                </p>
                <p class="advertisement-payment-warning">
                    DO NOT CLOSE THIS PAGE WITHOUT SEEING PAYMENT COMPLETED TEXT; OTHERWISE, YOU WILL LOSE ALL MONERO YOU PAID. ONLY USE THE REFRESH BUTTON BELOW TO CHECK FOR INCOMING TRANSACTIONS.
                </p>
                <p class="advertisement-payment-note" style="text-align: center;">
                    Click the refresh button above to check for new transactions.
                </p>
            </div>
        @endif

        <div class="advertisement-payment-footer">
            <a href="{{ route('vendor.my-products') }}" class="advertisement-payment-back-btn">
                Return to My Products
            </a>
        </div>
    </div>
</div>
@endsection
