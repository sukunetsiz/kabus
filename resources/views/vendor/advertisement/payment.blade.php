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
                        Payment window expires in:
                        <span id="countdown" data-expires="{{ $advertisement->expires_at->timestamp }}">
                            {{ $advertisement->expires_at->diffForHumans() }}
                        </span>
                    </p>
                </div>
            </div>
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
                    The payment will be automatically detected once confirmed on the blockchain.
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

@if(!$advertisement->payment_completed)
    @push('scripts')
    <script>
        function updateCountdown() {
            const expiresAt = document.getElementById('countdown').dataset.expires * 1000;
            const now = new Date().getTime();
            const distance = expiresAt - now;

            if (distance < 0) {
                document.getElementById('countdown').textContent = 'Expired';
                return;
            }

            const hours = Math.floor(distance / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById('countdown').textContent = 
                `${hours}h ${minutes}m ${seconds}s`;
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);

        // Refresh page every 30 seconds to check payment status
        setTimeout(() => {
            window.location.reload();
        }, 30000);
    </script>
    @endpush
@endif
@endsection
