@extends('layouts.app')
@section('content')

<div class="my-products-index-container">
    <div class="my-products-index-card">
        <div class="my-products-index-header">
            <h1>My Products</h1>
        </div>
        
        @if($products->isEmpty())
            <div class="my-products-index-empty">
                <p>You haven't added any products yet.</p>
            </div>
        @else
            <div class="my-products-index-grid">
                @foreach($products as $product)
                    <div class="my-products-index-product-card">
                        <h3 class="my-products-index-product-name" title="{{ $product->name }}">
                            <a href="{{ route('products.show', $product->slug) }}">
                                {{ \Str::limit($product->name, 30) }}
                            </a>
                        </h3>
                        <span class="my-products-index-product-type {{ strtolower($product->type) }}">
                            {{ $product->type === 'deaddrop' ? 'Dead Drop' : ucfirst($product->type) }}
                        </span>
                        <div class="my-products-index-actions">
                            <button class="my-products-index-btn my-products-index-btn-edit">
                               ⛔ Edit ⛔
                            </button>
                            <button class="my-products-index-btn my-products-index-btn-delete">
                               ⛔ Delete ⛔
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
