@props([
    'products'  // Required: collection of products
])
    <div class="product-card-grid">
        @foreach($products as $product)
            <a href="{{ route('products.show', $product->slug) }}" class="product-card">
                <div class="product-card-image">
                    <img src="{{ $product->product_picture_url }}" alt="{{ $product->name }}">
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

    <div class="product-card-pagination">
        {{ $products->links() }}
    </div>
