@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Breadcrumb Navigation --}}
    <div class="mb-6">
        <nav class="flex text-slate-400 text-sm">
            <a href="{{ route('products.index') }}" class="hover:text-orange-400 transition-colors duration-200">Products</a>
            <span class="mx-2">/</span>
            <span class="text-slate-200">Shopping Cart</span>
        </nav>
    </div>

    {{-- Error Messages --}}
    @if(session('error'))
        <div class="bg-red-900 text-red-200 p-4 rounded-lg mb-6">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Success Messages --}}
    @if(session('success'))
        <div class="bg-green-900 text-green-200 p-4 rounded-lg mb-6">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if($cartItems->isEmpty())
        <div class="bg-slate-800 rounded-lg p-8 text-center">
            <h2 class="text-2xl font-semibold text-slate-200 mb-2">Your Cart is Empty</h2>
            <p class="text-slate-400 mb-6">Browse our products and add items to your cart.</p>
            <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition-colors duration-200">
                Browse Products
            </a>
        </div>
    @else
        <div class="bg-slate-800 rounded-lg overflow-hidden shadow-lg">
            <div class="p-6">
                {{-- Cart Items --}}
                <div class="space-y-6">
                    @foreach($cartItems as $item)
                        <div class="flex flex-col md:flex-row gap-4 p-4 bg-slate-700 rounded-lg">
                            {{-- Product Image --}}
                            <div class="w-full md:w-32 h-32">
                                <img src="{{ $item->product->product_picture_url }}" 
                                     alt="{{ $item->product->name }}"
                                     class="w-full h-full object-cover rounded-lg">
                            </div>
                            
                            {{-- Product Details --}}
                            <div class="flex-grow">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-semibold text-slate-200">
                                            {{ $item->product->name }}
                                            <span class="text-sm text-slate-400">
                                                ({{ $item->selected_bulk_option ? ($item->quantity * $item->selected_bulk_option['amount']) : $item->quantity }} {{ $item->product->measurement_unit }})
                                            </span>
                                        </h3>
                                        <p class="text-sm text-slate-400">
                                            Sold by: {{ $item->product->user->username }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-semibold text-orange-400">
                                            ${{ number_format($item->getTotalPrice(), 2) }}
                                        </div>
                                        @if(is_numeric($xmrPrice) && $xmrPrice > 0)
                                            <div class="text-sm text-slate-400">
                                                ≈ ɱ{{ number_format($item->getTotalPrice() / $xmrPrice, 4) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Quantity and Options --}}
                                <div class="mt-4 flex flex-wrap gap-4">
                                    {{-- Quantity Form --}}
                                    @if($item->selected_bulk_option)
                                        <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center gap-2">
                                            @csrf
                                            @method('PUT')
                                            <label for="quantity_{{ $item->id }}" class="text-sm text-slate-400">Number of Sets:</label>
                                            <div class="flex items-center gap-2">
                                                <input type="number" 
                                                       id="quantity_{{ $item->id }}"
                                                       name="quantity" 
                                                       value="{{ $item->quantity }}"
                                                       min="1"
                                                       class="w-20 rounded border-gray-600 bg-slate-600 text-slate-200 text-center">
                                                <button type="submit" 
                                                        class="px-2 py-1 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded transition-colors duration-200">
                                                    Update
                                                </button>
                                            </div>
                                        </form>
                                    @else
                                        <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center gap-2">
                                            @csrf
                                            @method('PUT')
                                            <label for="quantity_{{ $item->id }}" class="text-sm text-slate-400">Quantity:</label>
                                            <div class="flex items-center gap-2">
                                                <input type="number" 
                                                       id="quantity_{{ $item->id }}"
                                                       name="quantity" 
                                                       value="{{ $item->quantity }}"
                                                       min="1"
                                                       class="w-20 rounded border-gray-600 bg-slate-600 text-slate-200 text-center">
                                                <button type="submit" 
                                                        class="px-2 py-1 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded transition-colors duration-200">
                                                    Update
                                                </button>
                                            </div>
                                        </form>
                                    @endif

                                    {{-- Selected Options --}}
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-slate-400">
                                            Delivery: {{ $item->selected_delivery_option['description'] }}
                                            ({{ $item->selected_delivery_option['price'] > 0 ? '+$' . number_format($item->selected_delivery_option['price'], 2) : 'Free' }})
                                        </span>
                                    </div>

                                    @if($item->selected_bulk_option)
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm text-slate-400">
                                                Bulk Deal: {{ $item->quantity }} sets of {{ $item->selected_bulk_option['amount'] }} {{ $item->product->measurement_unit }}
                                                ({{ $item->quantity * $item->selected_bulk_option['amount'] }} total)
                                                at ${{ number_format($item->selected_bulk_option['price'], 2) }} per set
                                            </span>
                                        </div>
                                    @endif

                                    {{-- Remove Button --}}
                                    <form action="{{ route('cart.destroy', $item) }}" method="POST" class="ml-auto">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300 text-sm">
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Cart Summary --}}
                <div class="mt-8 border-t border-slate-700 pt-6">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-lg text-slate-200">Total:</span>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-orange-400">
                                ${{ number_format($cartTotal, 2) }}
                            </div>
                            @if(is_numeric($xmrTotal))
                                <div class="text-slate-400">
                                    ≈ ɱ{{ number_format($xmrTotal, 4) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4">
                        {{-- Clear Cart Button --}}
                        <form action="{{ route('cart.clear') }}" method="POST" class="flex-grow">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200">
                                Clear Cart
                            </button>
                        </form>

                        {{-- Checkout Button --}}
                        <a href="{{ route('cart.checkout') }}" 
                           class="flex-grow px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200 text-center">
                            Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection