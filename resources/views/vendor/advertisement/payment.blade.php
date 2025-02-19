@extends('layouts.app')

@section('content')
<div>
    <div>
        <h1>Advertisement Payment</h1>

        <div>
            <!-- Payment Details -->
            <div>
                <h2>Payment Details</h2>
                <div>
                    <div>
                        <span>Product:</span>
                        <span>{{ $advertisement->product->name }}</span>
                    </div>
                    
                    <div>
                        <span>Selected Slot:</span>
                        <span>Slot {{ $advertisement->slot_number }}</span>
                    </div>
                    
                    <div>
                        <span>Duration:</span>
                        <span>{{ $advertisement->duration_days }} {{ Str::plural('day', $advertisement->duration_days) }}</span>
                    </div>
                    
                    <div>
                        <span>Required Amount:</span>
                        <span>ɱ{{ number_format($advertisement->required_amount, 12) }} XMR</span>
                    </div>

                    <div>
                        <span>Payment Status:</span>
                        <div>
                            @if($advertisement->payment_completed)
                                <span>
                                    Payment Completed
                                </span>
                            @elseif($advertisement->total_received > 0 && $advertisement->total_received < $advertisement->required_amount)
                                <span>
                                    Insufficient Amount
                                </span>
                            @else
                                <span>
                                    Awaiting Payment
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($advertisement->total_received > 0 && !$advertisement->payment_completed)
                        <div>
                            <span>Amount Received:</span>
                            <span>ɱ{{ number_format($advertisement->total_received, 12) }} XMR</span>
                            <span>
                                Remaining: ɱ{{ number_format($advertisement->required_amount - $advertisement->total_received, 12) }} XMR
                            </span>
                        </div>
                    @endif

                    <div>
                        <span>Payment Address:</span>
                        <div>
                            {{ $advertisement->payment_address }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- QR Code -->
            <div>
                <h2>Scan QR Code</h2>
                @if($qrCode)
                    <div>
                        <img src="{{ $qrCode }}" alt="Payment QR Code">
                    </div>
                @endif
                
                <div>
                    <p>
                        Payment window expires: {{ $advertisement->expires_at->diffForHumans() }}
                    </p>
                </div>
            </div>

            @if(!$advertisement->payment_completed)
                <div>
                    <a href="{{ route('vendor.advertisement.payment', $advertisement->payment_identifier) }}" class="btn btn-primary">
                        Refresh to check for new transactions
                    </a>
                </div>
            @endif
        </div>

        @if($advertisement->payment_completed)
            <div>
                <p>
                    Your payment has been completed successfully. Your advertisement will be displayed in slot {{ $advertisement->slot_number }} 
                    for {{ $advertisement->duration_days }} {{ Str::plural('day', $advertisement->duration_days) }}.
                </p>
            </div>
        @else
            <div>
                <p>
                    Please send exactly ɱ{{ number_format($advertisement->required_amount, 12) }} XMR to the address above. 
                    The payment will be detected once confirmed on the blockchain.
                </p>
                <p>
                    DO NOT CLOSE THIS PAGE WITHOUT SEEING PAYMENT COMPLETED TEXT; OTHERWISE, YOU WILL LOSE ALL MONERO YOU PAID. ONLY USE THE REFRESH BUTTON BELOW TO CHECK FOR INCOMING TRANSACTIONS.
                </p>
                <p>
                    Click the refresh button above to check for new transactions.
                </p>
            </div>
        @endif

        <div>
            <a href="{{ route('vendor.my-products') }}">
                Return to My Products
            </a>
        </div>
    </div>
</div>
@endsection
