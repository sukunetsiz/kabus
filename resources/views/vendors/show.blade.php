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
                        <div class="vendors-show-username">
                            <a href="{{ route('dashboard', $vendor->username) }}" class="vendors-show-value">
                                {{ $vendor->username }}
                            </a>
                        </div>
                        <div class="vendors-show-avatar">
                            <img src="{{ $vendor->profile ? $vendor->profile->profile_picture_url : asset('images/default-profile-picture.png') }}" 
                                 alt="{{ $vendor->username }}'s Profile Picture">
                        </div>
                        <div class="vendors-show-pgp-badge @if(!$vendor->pgpKey) vendors-show-pgp-badge--none @elseif($vendor->pgpKey->verified) vendors-show-pgp-badge--confirmed @else vendors-show-pgp-badge--unconfirmed @endif">
                            @if(!$vendor->pgpKey)
                                No PGP Key
                            @elseif($vendor->pgpKey->verified)
                                Verified PGP Key
                            @else
                                Unverified PGP Key
                            @endif
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

            {{-- Vendor Review Statistics Section --}}
            <div class="vendors-show-review-stats">
                <h2>Review Statistics</h2>
                
                @if($totalReviews > 0)
                    <div class="vendors-show-review-percentage">
                        <span class="vendors-show-review-percentage-value">{{ number_format($positivePercentage, 1) }}%</span>
                        <span class="vendors-show-review-percentage-label">Positive Reviews</span>
                    </div>
                    <div class="vendors-show-review-counts">
                        <div class="vendors-show-review-count-item">
                            <span class="vendors-show-review-count-label">Positive</span>
                            <span class="vendors-show-review-count-value">{{ $positiveCount }}</span>
                        </div>
                        <div class="vendors-show-review-count-item">
                            <span class="vendors-show-review-count-label">Mixed</span>
                            <span class="vendors-show-review-count-value">{{ $mixedCount }}</span>
                        </div>
                        <div class="vendors-show-review-count-item">
                            <span class="vendors-show-review-count-label">Negative</span>
                            <span class="vendors-show-review-count-value">{{ $negativeCount }}</span>
                        </div>
                        <div class="vendors-show-review-count-item">
                            <span class="vendors-show-review-count-label">Total</span>
                            <span class="vendors-show-review-count-value">{{ $totalReviews }}</span>
                        </div>
                    </div>
                @else
                    <div class="vendors-show-review-empty">
                        <p>No reviews yet for this vendor's products.</p>
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

            {{-- Vendor Policy Section --}}
            @if($vendor->vendorProfile && $vendor->vendorProfile->vendor_policy)
                <div class="vendors-show-policy">
                    <h2>Vendor Policy</h2>
                    <div class="vendors-show-policy-content">
                        <p>{{ $vendor->vendorProfile->vendor_policy }}</p>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
