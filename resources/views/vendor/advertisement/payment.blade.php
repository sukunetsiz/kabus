@extends('layouts.app')

@section('title', 'Advertisement Payment')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold mb-6">Advertisement Payment</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Payment Details -->
            <div>
                <h2 class="text-xl font-semibold mb-4">Payment Details</h2>
                <div class="space-y-4">
                    <div>
                        <span class="block text-gray-600">Product:</span>
                        <span class="font-medium">{{ $advertisement->product->name }}</span>
                    </div>
                    
                    <div>
                        <span class="block text-gray-600">Selected Slot:</span>
                        <span class="font-medium">Slot {{ $advertisement->slot_number }}</span>
                    </div>
                    
                    <div>
                        <span class="block text-gray-600">Duration:</span>
                        <span class="font-medium">{{ $advertisement->duration_days }} {{ Str::plural('day', $advertisement->duration_days) }}</span>
                    </div>
                    
                    <div>
                        <span class="block text-gray-600">Required Amount:</span>
                        <span class="font-medium">ɱ{{ number_format($advertisement->required_amount, 12) }} XMR</span>
                    </div>

                    <div class="mt-6">
                        <span class="block text-gray-600 mb-2">Payment Status:</span>
                        <div class="flex items-center space-x-2">
                            @if($advertisement->payment_completed)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    Payment Completed
                                </span>
                            @elseif($advertisement->total_received > 0 && $advertisement->total_received < $advertisement->required_amount)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    Insufficient Amount
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    Awaiting Payment
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($advertisement->total_received > 0 && !$advertisement->payment_completed)
                        <div class="mt-4">
                            <span class="block text-gray-600">Amount Received:</span>
                            <span class="font-medium">ɱ{{ number_format($advertisement->total_received, 12) }} XMR</span>
                            <span class="block text-sm text-gray-500 mt-1">
                                Remaining: ɱ{{ number_format($advertisement->required_amount - $advertisement->total_received, 12) }} XMR
                            </span>
                        </div>
                    @endif

                    <div class="mt-4">
                        <span class="block text-gray-600">Payment Address:</span>
                        <div class="mt-2 p-4 bg-gray-50 rounded-lg break-all font-mono text-sm">
                            {{ $advertisement->payment_address }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- QR Code -->
            <div class="flex flex-col items-center justify-start">
                <h2 class="text-xl font-semibold mb-4">Scan QR Code</h2>
                @if($qrCode)
                    <div class="bg-white p-4 rounded-lg shadow-sm">
                        <img src="{{ $qrCode }}" alt="Payment QR Code" class="max-w-full">
                    </div>
                @endif
                
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Payment window expires in:
                        <span class="font-medium" id="countdown" data-expires="{{ $advertisement->expires_at->timestamp }}">
                            {{ $advertisement->expires_at->diffForHumans() }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        @if($advertisement->payment_completed)
            <div class="mt-8 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-green-800">
                    Your payment has been completed successfully. Your advertisement will be displayed in slot {{ $advertisement->slot_number }} 
                    for {{ $advertisement->duration_days }} {{ Str::plural('day', $advertisement->duration_days) }}.
                </p>
            </div>
        @else
            <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-blue-800">
                    Please send exactly ɱ{{ number_format($advertisement->required_amount, 12) }} XMR to the address above. 
                    The payment will be automatically detected once confirmed on the blockchain.
                </p>
            </div>
        @endif

        <div class="mt-8 text-center">
            <a href="{{ route('vendor.my-products') }}" class="text-blue-600 hover:text-blue-800">
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