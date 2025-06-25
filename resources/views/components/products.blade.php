@props([
    'products'  // Required: collection of products
])
<div class="product-card-grid">
    @foreach($products as $product)
        <a href="{{ route('products.show', $product->slug) }}" class="product-card">
            <div class="product-card-info-review">
                @if($product->getPositiveReviewPercentage() !== null)
                    <span class="product-card-review-star">★</span>
                    <span class="product-card-info-text">
                        {{ number_format($product->getPositiveReviewPercentage(), 0) }}%
                    </span>
                @else
                    <span class="product-card-info-text">
                        No Reviews
                    </span>
                @endif
            </div>
            <div class="product-card-info-username">
                <span class="product-card-info-text">
                    {{ $product->user->username }}
                </span>
            </div>
            @auth
            <form action="{{ Auth::user()->hasWishlisted($product->id) 
                ? route('wishlist.destroy', $product) 
                : route('wishlist.store', $product) }}" 
                method="POST">
                @csrf
                @if(Auth::user()->hasWishlisted($product->id))
                    @method('DELETE')
                @endif
                <button type="submit" class="product-card-wishlist-button {{ Auth::user()->hasWishlisted($product->id) ? 'active' : '' }}" title="{{ Auth::user()->hasWishlisted($product->id) ? 'Remove from Wishlist' : 'Add to Wishlist' }}">
                    <span class="product-card-wishlist-heart">❤️</span>
                </button>
            </form>
            @endauth
            <div class="product-card-image">
                <img src="{{ $product->product_picture_url }}" alt="{{ $product->name }}">
            </div>
            <div class="product-card-content">
                <div class="product-card-header">
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
<div class="product-card-pagination">
    {{ $products->links() }}
</div>
