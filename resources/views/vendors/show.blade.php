@extends('layouts.app')

@section('content')
<div class="vendors-show-container">
    @if($vacation_mode)
        <div class="vendors-show-vacation-notice">
            <h2>Vendor is Currently on Vacation</h2>
            <p>This vendor is currently taking a break. Please check back later.</p>
        </div>
    @else
        <div class="vendors-show-main-card">
            {{-- Vendor Header --}}
            <div class="vendors-show-header">
                <h1>Vendor Profile</h1>
                <a href="{{ route('vendors.index') }}" class="vendors-show-back-btn">
                    Back to Vendors
                </a>
            </div>
            
            {{-- Vendor Profile Section --}}
            <div class="vendors-show-profile">
                <div class="vendors-show-profile-header">
                    <div class="vendors-show-avatar-username">
                        <div class="vendors-show-avatar">
                            <img src="{{ $vendor->profile ? $vendor->profile->profile_picture_url : asset('images/default-profile-picture.png') }}" 
                                 alt="{{ $vendor->username }}'s Profile Picture">
                        </div>
                        <div class="vendors-show-username">
                            <a href="{{ route('dashboard', $vendor->username) }}" class="vendors-show-value">
                                {{ $vendor->username }}
                            </a>
                        </div>
                    </div>
                </div>

                @if($vendor->vendorProfile && $vendor->vendorProfile->description)
                    <div class="vendors-show-description">
                        <h2>Store Description</h2>
                        <div class="vendors-show-description-content">
                            <p>{{ $vendor->vendorProfile->description }}</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Products Section --}}
            <div class="vendors-show-products">
                <h2>Products Offered by {{ $vendor->username }}</h2>

                @if($products->isEmpty())
                    <div class="vendors-show-empty">
                        <p>No products available at the moment.</p>
                    </div>
                @else
                    <div class="vendors-show-grid">
                        @foreach($products as $product)
                            <a href="{{ route('products.show', $product->slug) }}" class="product-card">
                                {{-- Product Picture --}}
                                <div class="product-card-image">
                                    <img src="{{ $product->product_picture_url }}" 
                                         alt="{{ $product->name }}">
                                </div>
                                <div class="product-card-content">
                                    <div class="product-card-header">
                                        <span class="product-card-type {{ $product->type === 'digital' ? 'type-digital' : ($product->type === 'cargo' ? 'type-cargo' : 'type-deaddrop') }}">
                                            @if($product->type === 'digital')
                                                Digital
                                            @elseif($product->type === 'cargo')
                                                Cargo
                                            @else
                                                Dead Drop
                                            @endif
                                        </span>
                                        <span class="product-card-price">
                                            ${{ number_format($product->price, 2) }}
                                        </span>
                                    </div>
                                    
                                    <div class="product-card-name-container">
                                        <h3 class="product-card-name">
                                            {{ $product->name }}
                                        </h3>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="vendors-show-pagination">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

@endsection
