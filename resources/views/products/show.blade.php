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
                        <span class="text-3xl font-bold text-orange-400">${{ number_format($product->price, 2) }}</span>
                        <span class="text-slate-400 text-sm mt-1">Listed by <span class="text-orange-400">{{ $product->user->username }}</span></span>
                        
                        {{-- Wishlist Button --}}
                        @if(Auth::user()->hasWishlisted($product->id))
                            <form action="{{ route('wishlist.destroy', $product) }}" method="POST" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                    Remove from Wishlist
                                </button>
                            </form>
                        @else
                            <form action="{{ route('wishlist.store', $product) }}" method="POST" class="mt-2">
                                @csrf
                                <button type="submit" 
                                        class="bg-orange-600 hover:bg-orange-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                    Add to Wishlist
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

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
                        <span>{{ number_format($product->stock_amount) }} {{ $product->measurement_unit }}</span>
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
                        {{-- Product Type Information --}}
                        <p class="text-slate-300">
                            @if($product->isDigital())
                                This is a digital product. Select your preferred processing time:
                            @elseif($product->isCargo())
                                This product will be shipped via secure cargo delivery. Select your preferred shipping method:
                            @else
                                This is a dead drop delivery. Select your preferred pickup window:
                            @endif
                        </p>

                        {{-- Delivery Options Dropdown --}}
                        <div class="mt-4">
                            <select name="delivery_option" id="delivery_option" 
                                class="w-full rounded-lg border-gray-600 bg-slate-700 text-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach($product->getFormattedDeliveryOptions() as $index => $option)
                                    <option value="{{ $index }}">
                                        {{ $option['description'] }}
                                        @if($option['price'] > 0)
                                            (+${{ $option['price'] }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Option Details --}}
                        <div class="mt-4 text-sm text-slate-400">
                            <p>* Additional fees (if any) will be added to the base price</p>
                            @if($product->isDigital())
                                <p>* Access details will be provided after purchase</p>
                            @elseif($product->isCargo())
                                <p>* Shipping details will be provided after purchase</p>
                            @else
                                <p>* Pickup location will be provided after purchase</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
