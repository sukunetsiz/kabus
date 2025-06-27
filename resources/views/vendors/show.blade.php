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
            <div class="vendors-show-statistics-stats">
                <h2>Review Statistics</h2>
                
                @if($totalReviews > 0)
                    <div class="vendors-show-statistics-percentage">
                        <span class="vendors-show-statistics-percentage-value">{{ number_format($positivePercentage, 1) }}%</span>
                        <span class="vendors-show-statistics-percentage-label">Positive Reviews</span>
                    </div>
                    <div class="vendors-show-statistics-counts">
                        <div class="vendors-show-statistics-count-item">
                            <span class="vendors-show-statistics-count-label">Positive</span>
                            <span class="vendors-show-statistics-count-value">{{ $positiveCount }}</span>
                        </div>
                        <div class="vendors-show-statistics-count-item">
                            <span class="vendors-show-statistics-count-label">Mixed</span>
                            <span class="vendors-show-statistics-count-value">{{ $mixedCount }}</span>
                        </div>
                        <div class="vendors-show-statistics-count-item">
                            <span class="vendors-show-statistics-count-label">Negative</span>
                            <span class="vendors-show-statistics-count-value">{{ $negativeCount }}</span>
                        </div>
                        <div class="vendors-show-statistics-count-item">
                            <span class="vendors-show-statistics-count-label">Total</span>
                            <span class="vendors-show-statistics-count-value">{{ $totalReviews }}</span>
                        </div>
                    </div>
                @else
                    <div class="vendors-show-statistics-empty">
                        <p>No reviews yet for this vendor's products.</p>
                    </div>
                @endif
            </div>

            {{-- Vendor Dispute Statistics Section --}}
            <div class="vendors-show-statistics-stats">
                <h2>Dispute Statistics</h2>
                
                @if($totalDisputes > 0)
                    <div class="vendors-show-statistics-counts">
                        <div class="vendors-show-statistics-count-item">
                            <span class="vendors-show-statistics-count-label">Won</span>
                            <span class="vendors-show-statistics-count-value">{{ $disputesWon }}</span>
                        </div>
                        <div class="vendors-show-statistics-count-item">
                            <span class="vendors-show-statistics-count-label">Open</span>
                            <span class="vendors-show-statistics-count-value">{{ $disputesOpen }}</span>
                        </div>
                        <div class="vendors-show-statistics-count-item">
                            <span class="vendors-show-statistics-count-label">Lost</span>
                            <span class="vendors-show-statistics-count-value">{{ $disputesLost }}</span>
                        </div>
                        <div class="vendors-show-statistics-count-item">
                            <span class="vendors-show-statistics-count-label">Total</span>
                            <span class="vendors-show-statistics-count-value">{{ $totalDisputes }}</span>
                        </div>
                    </div>
                @else
                    <div class="vendors-show-statistics-empty">
                        <p>No disputes yet for this vendor.</p>
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
                    <x-products 
                        :products="$products"
                    />
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
        
        {{-- Vendor's All Reviews Section --}}
        @if(!$vacation_mode && isset($allReviews) && !$allReviews->isEmpty())
            <h2 class="vendors-show-reviews-title">{{ $vendor->username }}'s All Reviews</h2>
        
            <div class="products-show-reviews-list">
                @foreach($allReviews as $review)
                    <div class="products-show-review-item">
                        <div class="products-show-review-header">
                            <div class="products-show-review-user">
                                <div class="products-show-review-avatar">
                                    <img src="{{ $review->user->profile ? $review->user->profile->profile_picture_url : asset('images/default-profile-picture.png') }}" 
                                         alt="{{ $review->user->username }}'s Profile Picture">
                                </div>
                                <div class="products-show-review-username">
                                    <div>{{ $review->user->username }}'s review for 
                                        <a href="{{ route('products.show', $review->product->slug) }}" class="vendors-show-product-link">{{ $review->product->name }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="products-show-review-meta">
                                <div class="products-show-review-date">{{ $review->getFormattedDate() }}</div>
                                <div class="products-show-review-sentiment products-show-review-sentiment-{{ strtolower($review->sentiment) }}">
                                    {{ ucfirst($review->sentiment) }}
                                </div>
                            </div>
                        </div>
                        <div class="products-show-review-content">
                            {{ $review->review_text }}
                        </div>
                    </div>
                @endforeach
            </div>
    
            {{-- Reviews Pagination --}}
            <div class="vendors-show-pagination">
                {{ $allReviews->appends(request()->except('reviews_page'))->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
