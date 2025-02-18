@extends('layouts.app')

@section('title', 'Create Advertisement')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold mb-6">Create Advertisement</h1>

        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-4">Product Details</h2>
            <div class="flex items-start space-x-4">
                <div class="w-32 h-32">
                    <img src="{{ $product->product_picture_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded">
                </div>
                <div>
                    <h3 class="text-lg font-medium">{{ $product->name }}</h3>
                    <p class="text-gray-600 mt-2">{{ Str::limit($product->description, 150) }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('vendor.advertisement.store', $product) }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <h2 class="text-xl font-semibold mb-4">Select Advertisement Slot</h2>
                <p class="text-gray-600 mb-4">Choose one of the available slots below. Pricing varies by slot position.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach(config('monero.advertisement_slot_multipliers') as $slot => $multiplier)
                        @php
                            $basePrice = config('monero.advertisement_base_price');
                            $price = $basePrice * $multiplier;
                            $isAvailable = !\App\Models\Advertisement::where('slot_number', $slot)
                                ->where('payment_completed', true)
                                ->where('starts_at', '<=', now())
                                ->where('ends_at', '>=', now())
                                ->exists();
                        @endphp
                        
                        <div class="relative">
                            <label class="flex items-start p-4 border rounded-lg @if(!$isAvailable) opacity-50 @endif">
                                <input type="radio" 
                                       name="slot_number" 
                                       value="{{ $slot }}" 
                                       class="mt-1"
                                       @if(!$isAvailable) disabled @endif
                                       @if(old('slot_number') == $slot) checked @endif
                                       required>
                                <div class="ml-3">
                                    <span class="block font-medium">Slot {{ $slot }}</span>
                                    <span class="block text-sm text-gray-600">
                                        ɱ{{ number_format($price, 3) }} XMR per day
                                    </span>
                                    @if(!$isAvailable)
                                        <span class="text-red-600 text-sm">Currently occupied</span>
                                    @endif
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
                
                @error('slot_number')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <h2 class="text-xl font-semibold mb-4">Select Duration</h2>
                <p class="text-gray-600 mb-4">Choose how many days you want your advertisement to run.</p>
                
                <div class="w-full md:w-1/3">
                    <select name="duration_days" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            required>
                        @for($i = config('monero.advertisement_min_duration'); $i <= config('monero.advertisement_max_duration'); $i++)
                            <option value="{{ $i }}" @if(old('duration_days') == $i) selected @endif>
                                {{ $i }} {{ Str::plural('day', $i) }}
                            </option>
                        @endfor
                    </select>
                    
                    @error('duration_days')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="border-t pt-6">
                <button type="submit" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Proceed to Payment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection