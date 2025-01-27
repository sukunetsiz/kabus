@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Breadcrumb Navigation --}}
    <div class="mb-6">
        <nav class="flex text-slate-400 text-sm">
            <a href="{{ route('products.index') }}" class="hover:text-orange-400 transition-colors duration-200">Products</a>
            <span class="mx-2">/</span>
            <span class="text-slate-200">{{ $product->name }}</span>
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

    @if($vendor_on_vacation)
        <div class="max-w-2xl mx-auto bg-slate-800 rounded-lg shadow-lg p-6 text-center">
            <h2 class="text-2xl font-semibold text-slate-200 mb-3">Product Currently Unavailable</h2>
            <p class="text-slate-300">This product is temporarily unavailable as the vendor is currently on vacation. Please check back later.</p>
        </div>
    @else
        {{-- Product Details --}}
        <div class="bg-slate-800 rounded-lg overflow-hidden shadow-lg">
            <div class="p-6 md:p-8">
                {{-- Product Image --}}
                <div class="mb-6">
                    <img src="{{ $product->product_picture_url }}" 
                         alt="{{ $product->name }}"
                         class="w-full max-w-md mx-auto rounded-lg shadow-lg object-cover">
                </div>
                
                <div class="flex flex-col md:flex-row justify-between mb-6">
                    <div class="mb-4 md:mb-0">
                        <h1 class="text-2xl md:text-3xl font-bold text-slate-200 mb-2">{{ $product->name }}</h1>
                        <div class="flex items-center space-x-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm 
                                {{ $product->type === 'digital' ? 'bg-purple-900 text-purple-200' : 
                                   ($product->type === 'cargo' ? 'bg-blue-900 text-blue-200' : 
                                    'bg-green-900 text-green-200') }}">
                                @if($product->type === 'digital')
                                    Digital
                                @elseif($product->type === 'cargo')
                                    Cargo
                                @else
                                    Dead Drop
                                @endif
                            </span>
                            <span class="text-slate-400">
                                Category: <span class="text-orange-400">{{ $product->category->name }}</span>
                            </span>
                        </div>
                    </div>
                    <div class="flex flex-col items-end">
                        <div class="space-y-1">
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl font-bold text-orange-400">${{ number_format($product->price, 2) }}</span>
                                @if(is_numeric($xmrPrice))
                                    <span class="text-xl font-semibold text-slate-300">
                                        ≈ ɱ{{ number_format($xmrPrice, 4) }}
                                    </span>
                                @endif
                            </div>
                            <div class="text-sm">
                                @if($xmrPrice === 'UNAVAILABLE')
                                    <span class="text-red-400">XMR PRICE UNAVAILABLE</span>
                                @else
                                    <span class="text-slate-400">
                                        Listed by <span class="text-orange-400">{{ $product->user->username }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex flex-col gap-2">
                            {{-- Wishlist Button --}}
                            @if(Auth::user()->hasWishlisted($product->id))
                                <form action="{{ route('wishlist.destroy', $product) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                        Remove from Wishlist
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('wishlist.store', $product) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="bg-orange-600 hover:bg-orange-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                        Add to Wishlist
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                @if(Auth::id() === $product->user_id)
                    <div class="mt-6 border-t border-slate-700 pt-6">
                        <div class="bg-red-900 text-red-200 p-4 rounded-lg">
                            <p>You cannot add your own products to the cart.</p>
                        </div>
                    </div>
                @else
                    {{-- Add to Cart Form --}}
                    <form action="{{ route('cart.store', $product) }}" method="POST" class="mt-6 border-t border-slate-700 pt-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Quantity Input --}}
                            <div>
                                <label for="quantity" class="block text-sm font-medium text-slate-300 mb-2">Quantity</label>
                                <div class="flex gap-2">
                                    <input type="number" 
                                           name="quantity" 
                                           id="quantity" 
                                           min="1" 
                                           value="{{ old('quantity', 1) }}" 
                                           class="w-full rounded-lg border-gray-600 bg-slate-700 text-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           required>
                                </div>
                            </div>

                            {{-- Delivery Options Select --}}
                            <div>
                                <label for="delivery_option" class="block text-sm font-medium text-slate-300 mb-2">Delivery Option</label>
                                <select name="delivery_option" 
                                        id="delivery_option" 
                                        class="w-full rounded-lg border-gray-600 bg-slate-700 text-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required>
                                    @foreach($formattedDeliveryOptions as $index => $option)
                                        <option value="{{ $index }}">
                                            {{ $option['description'] }}
                                            @if(str_starts_with($option['price'], '$0.00'))
                                                (Free)
                                            @else
                                                ({{ $option['price'] }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @if($product->bulk_options && count($product->bulk_options) > 0)
                                {{-- Bulk Options Select --}}
                                <div class="md:col-span-2">
                                    <label for="bulk_option" class="block text-sm font-medium text-slate-300 mb-2">Bulk Purchase Option</label>
                                    <select name="bulk_option" 
                                            id="bulk_option" 
                                            class="w-full rounded-lg border-gray-600 bg-slate-700 text-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Regular Price (No Bulk Discount)</option>
                                        @foreach($formattedBulkOptions as $index => $option)
                                            <option value="{{ $index }}">
                                                {{ $option['display_text'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>

                        {{-- Add to Cart Button --}}
                        <div class="mt-6">
                            <button type="submit" 
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 text-center">
                                Add to Cart
                            </button>
                        </div>
                    </form>
                @endif

                {{-- Product Description --}}
                <div class="prose prose-invert max-w-none mb-8">
                    <div class="text-slate-300">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>

                {{-- Stock Information --}}
                <div class="bg-slate-900 rounded-lg p-4 mb-4">
                    <h3 class="text-lg font-semibold text-slate-200 mb-3">Stock Information</h3>
                    <div class="flex items-center space-x-2 text-slate-300">
                        <span class="font-medium">Available:</span>
                        <span>{{ number_format($product->stock_amount) }} {{ $formattedMeasurementUnit }}</span>
                    </div>
                </div>

                {{-- Delivery Options --}}
                <div class="bg-slate-900 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-slate-200 mb-3">
                        @if($product->isDigital())
                            Processing Options
                        @elseif($product->isCargo())
                            Shipping Options
                        @else
                            Pickup Options
                        @endif
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="mt-4 text-sm text-slate-400">
                            <p>* Additional fees (if any) will be added to the base price</p>
                        </div>
                    </div>
                </div>

                {{-- Bulk Options --}}
                @if($product->bulk_options && count($product->bulk_options) > 0)
                    <div class="bg-slate-900 rounded-lg p-4 mt-4">
                        <h3 class="text-lg font-semibold text-slate-200 mb-3">Bulk Purchase Options</h3>
                        
                        <div class="space-y-4">
                            <p class="text-slate-300">
                                Select a bulk purchase option to get a better price for larger quantities:
                            </p>

                            <div class="mt-4 text-sm text-slate-400">
                                <p>* Select a bulk option to save on larger purchases</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
