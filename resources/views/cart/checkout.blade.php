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

    {{-- Checkout Placeholder --}}
    <div class="bg-slate-800 rounded-lg p-8 text-center">

        <h2 class="text-2xl font-semibold text-slate-200 mb-4">Checkout Coming Soon</h2>
        <p class="text-slate-400 mb-6 max-w-lg mx-auto">
            The checkout functionality is currently under development. Please check back later.
        </p>
        <div class="flex justify-center gap-4">
            <a href="{{ route('cart.index') }}" 
               class="px-6 py-3 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-colors duration-200">
                Return to Cart
            </a>
            <a href="{{ route('products.index') }}" 
               class="px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition-colors duration-200">
                Continue Shopping
            </a>
        </div>
    </div>

    {{-- Cart Summary --}}
    @if($cartItems->isNotEmpty())
        <div class="mt-8 bg-slate-800 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-slate-200 mb-4">Order Summary</h3>
            <div class="space-y-4">
                @foreach($cartItems as $item)
                    <div class="flex justify-between text-slate-300">
                        <span>{{ $item->product->name }} (x{{ $item->quantity }})</span>
                        <span>${{ number_format($item->getTotalPrice(), 2) }}</span>
                    </div>
                @endforeach
                <div class="border-t border-slate-700 pt-4 flex justify-between text-lg font-semibold">
                    <span class="text-slate-200">Total</span>
                    <span class="text-orange-400">${{ number_format($cartItems->sum(function($item) { return $item->getTotalPrice(); }), 2) }}</span>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection