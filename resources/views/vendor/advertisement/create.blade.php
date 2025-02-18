@extends('layouts.app')

@section('content')
<div>
    <div>
        <h1>Create Advertisement</h1>

        <div>
            <h2>Product Details</h2>
            <div>
                <div>
                    <img src="{{ $product->product_picture_url }}" alt="{{ $product->name }}">
                </div>
                <div>
                    <h3>{{ $product->name }}</h3>
                    <p>{{ Str::limit($product->description, 150) }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('vendor.advertisement.store', $product) }}" method="POST">
            @csrf
            
            <div>
                <h2>Select Advertisement Slot</h2>
                <p>Choose one of the available slots below. Pricing varies by slot position.</p>
                
                <div>
                    @foreach($slots as $slot)
                        <div>
                            <label>
                                <input type="radio" 
                                       name="slot_number" 
                                       value="{{ $slot['number'] }}" 
                                       @if(!$slot['is_available']) disabled @endif
                                       @if(old('slot_number') == $slot['number']) checked @endif
                                       required>
                                <div>
                                    <span>Slot {{ $slot['number'] }}</span>
                                    <span>
                                        ɱ{{ number_format($slot['price'], 3) }} XMR per day
                                    </span>
                                    @if(!$slot['is_available'])
                                        <span>Currently occupied</span>
                                    @endif
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
                
                @error('slot_number')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <h2>Select Duration</h2>
                <p>Choose how many days you want your advertisement to run.</p>
                
                <div>
                    <select name="duration_days" required>
                        @for($i = config('monero.advertisement_min_duration'); $i <= config('monero.advertisement_max_duration'); $i++)
                            <option value="{{ $i }}" @if(old('duration_days') == $i) selected @endif>
                                {{ $i }} {{ Str::plural('day', $i) }}
                            </option>
                        @endfor
                    </select>
                    
                    @error('duration_days')
                        <p>{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <button type="submit">Proceed to Payment</button>
            </div>
        </form>
    </div>
</div>
@endsection
