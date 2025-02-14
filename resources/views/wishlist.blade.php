@extends('layouts.app')

@section('content')

<div class="wishlist-index-container">
    <div class="wishlist-index-header">
        <h1 class="wishlist-index-title">{{ $title }}</h1>
        @if(!$products->isEmpty())
            <form action="{{ route('wishlist.clear') }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="wishlist-index-clear-btn">
                    Clear Wishlist
                </button>
            </form>
        @endif
    </div>
    
    @if ($errors->any() || session('error'))
        <div class="alert alert-error">
            @if (session('error'))
                <p>{{ session('error') }}</p>
            @else
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if($products->isEmpty())
        <div class="wishlist-index-empty">
            <p class="wishlist-index-empty-text">Your wishlist is empty.</p>
            <a href="{{ route('products.index') }}" class="wishlist-index-browse-btn">
                Browse Products
            </a>
        </div>
    @else
        <div class="wishlist-index-grid">
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

        <div class="wishlist-index-pagination">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection

