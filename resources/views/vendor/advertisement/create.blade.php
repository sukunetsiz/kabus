@extends('layouts.app')

@section('content')

<div class="advertisement-create-container">
    <div class="advertisement-create-content">
        <h1 class="advertisement-create-title">Create Advertisement</h1>

        <div class="advertisement-create-product-section">
            <h2 class="advertisement-create-section-title">Product Details</h2>
            <div class="advertisement-create-product-card">
                <div class="advertisement-create-product-image">
                    <img src="{{ $product->product_picture_url }}" alt="{{ $product->name }}">
                </div>
                <div class="advertisement-create-product-info">
                    <h3 class="advertisement-create-product-name">{{ $product->name }}</h3>
                    <p class="advertisement-create-product-description">{{ Str::limit($product->description, 150) }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('vendor.advertisement.store', $product) }}" method="POST" class="advertisement-create-form">
            @csrf
            
            <div class="advertisement-create-section">
                <h2 class="advertisement-create-section-title">Select Advertisement Slot</h2>
                <p class="advertisement-create-section-description">Choose one of the available slots below. Pricing varies by slot position.</p>
                
                <div class="advertisement-create-slots">
                    @foreach($slots as $slot)
                        <div class="advertisement-create-slot-item">
                            <label class="advertisement-create-slot-label @if(!$slot['is_available']) advertisement-create-slot-disabled @endif">
                                <input type="radio" 
                                       class="advertisement-create-slot-input"
                                       name="slot_number" 
                                       value="{{ $slot['number'] }}" 
                                       @if(!$slot['is_available']) disabled @endif
                                       @if(old('slot_number') == $slot['number']) checked @endif
                                       required>
                                <div class="advertisement-create-slot-content">
                                    <span class="advertisement-create-slot-number">Slot {{ $slot['number'] }}</span>
                                    @if(!$slot['is_available'])
                                        <span class="advertisement-create-slot-status">Currently occupied</span>
                                    @endif
                                    <span class="advertisement-create-slot-price">
                                        ɱ{{ number_format($slot['price'], 3) }} XMR per day
                                    </span>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="advertisement-create-section">
                <h2 class="advertisement-create-section-title">Select Duration</h2>
                <p class="advertisement-create-section-description">Choose how many days you want your advertisement to run.</p>
                
                <div class="advertisement-create-duration">
                    <select name="duration_days" required class="advertisement-create-select">
                        @for($i = config('monero.advertisement_min_duration'); $i <= config('monero.advertisement_max_duration'); $i++)
                            <option value="{{ $i }}" @if(old('duration_days') == $i) selected @endif>
                                {{ $i }} {{ Str::plural('day', $i) }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="advertisement-create-submit">
                <button type="submit" class="advertisement-create-submit-btn">Proceed to Payment</button>
            </div>
        </form>
    </div>
</div>
@endsection
