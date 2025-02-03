@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Breadcrumb Navigation --}}
    <div class="mb-6">
        <nav class="flex text-slate-400 text-sm">
            <a href="{{ route('products.index') }}" class="hover:text-orange-400 transition-colors duration-200">Products</a>
            <span class="mx-2">/</span>
            <a href="{{ route('cart.index') }}" class="hover:text-orange-400 transition-colors duration-200">Cart</a>
            <span class="mx-2">/</span>
            <span class="text-slate-200">Checkout</span>
        </nav>
    </div>

    {{-- Checkout Details --}}
    <div class="bg-slate-800 rounded-lg p-8">
        <h2 class="text-2xl font-semibold text-slate-200 mb-6">Checkout Details</h2>
        
        <div class="flex justify-between gap-8">
            {{-- Order Details --}}
            <div class="flex-grow">
                <h3 class="text-lg font-semibold text-slate-200 mb-4">Order Information</h3>
                <div class="space-y-4 mb-6">
                    @if($cartItems->isNotEmpty())
                        @foreach($cartItems as $item)
                            <div class="flex justify-between text-slate-300">
                                <span>{{ $item->product->name }} (x{{ $item->quantity }})</span>
                                <span>${{ number_format($item->getTotalPrice(), 2) }}</span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-slate-400">No items in cart.</p>
                    @endif
                </div>
                {{-- Add order details form here in future updates --}}
            </div>

            {{-- Price Summary --}}
            <div class="w-96">
                <div class="bg-slate-700 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-slate-200 mb-4">Price Summary</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between text-slate-300">
                            <span>Subtotal</span>
                            <span>${{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-300">
                            <span>Commission ({{ $commissionPercentage }}%)</span>
                            <span>${{ number_format($commission, 2) }}</span>
                        </div>
                        <div class="border-t border-slate-600 pt-3 flex justify-between text-lg font-semibold">
                            <span class="text-slate-200">Total</span>
                            <span class="text-orange-400">${{ number_format($total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex gap-4">
                    <a href="{{ route('cart.index') }}" 
                       class="flex-1 px-6 py-3 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-colors duration-200 text-center">
                        Back to Cart
                    </a>
                    <button type="submit" 
                            class="flex-1 px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition-colors duration-200">
                        Proceed
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection