@extends('layouts.app')

@section('content')
<div class="products-index-container">
    <div class="products-index-content">
        {{-- Error Messages --}}
        @if ($errors->any() || session('error'))
            <div class="alert alert-error">
                @if (session('error'))
                    <p>{{ session('error') }}</p>
                @else
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif
        
        {{-- Search and Filter Form --}}
        <div class="products-index-filter-card">
            <form action="{{ route('products.index') }}" method="GET">
                {{-- Top Row: Vendor + Dropdowns --}}
                <div class="products-index-filter-row">
                    <div class="products-index-filter-left">
                        <div class="products-index-form-group">
                            <label for="vendor" class="products-index-label">By Vendor</label>
                            <input type="text" 
                                   name="vendor" 
                                   id="vendor" 
                                   value="{{ $filters['vendor'] ?? '' }}"
                                   placeholder="Search vendor 🔎"
                                   maxlength="50"
                                   class="products-index-input @error('vendor') is-invalid @enderror">
                        </div>
                    </div>
                    
                    <div class="products-index-filter-right">
                        <div class="products-index-form-group">
                            <label for="type" class="products-index-label">By Product Type</label>
                            <select name="type" 
                                    id="type" 
                                    class="products-index-select @error('type') is-invalid @enderror">
                                <option value="">All Types</option>
                                <option value="digital" {{ ($currentType === 'digital') ? 'selected' : '' }}>Digital</option>
                                <option value="cargo" {{ ($currentType === 'cargo') ? 'selected' : '' }}>Cargo</option>
                                <option value="deaddrop" {{ ($currentType === 'deaddrop') ? 'selected' : '' }}>Dead Drop</option>
                            </select>
                        </div>

                        <div class="products-index-form-group">
                            <label for="category" class="products-index-label">By Category</label>
                            <select name="category" 
                                    id="category" 
                                    class="products-index-select @error('category') is-invalid @enderror">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ ($filters['category'] ?? '') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="products-index-form-group">
                            <label for="sort_price" class="products-index-label">Sort by Price</label>
                            <select name="sort_price" 
                                    id="sort_price" 
                                    class="products-index-select @error('sort_price') is-invalid @enderror">
                                <option value="">Most Recent</option>
                                <option value="asc" {{ ($filters['sort_price'] ?? '') === 'asc' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="desc" {{ ($filters['sort_price'] ?? '') === 'desc' ? 'selected' : '' }}>Price: High to Low</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Middle Row: Main Search --}}
                <div class="products-index-search-container">
                    <div class="products-index-main-search">
                        <label for="search" class="products-index-main-search-label">Search Product</label>
                        <input type="text" 
                               name="search" 
                               id="search" 
                               value="{{ $filters['search'] ?? '' }}"
                               placeholder="Search by product title 🔎"
                               maxlength="100"
                               class="products-index-main-search-input @error('search') is-invalid @enderror">
                    </div>
                </div>

                {{-- Bottom Row: Buttons --}}
                <div class="products-index-filter-actions">
                    <a href="{{ route('products.index') }}" class="products-index-button products-index-button-secondary">
                        Reset Filters
                    </a>
                    <button type="submit" class="products-index-button products-index-button-primary">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($products->isEmpty())
        <div class="products-index-empty">
            <p>No products found.</p>
        </div>
    @else
        <div class="products-index-grid">
            @foreach($products as $product)
                <a href="{{ route('products.show', $product->slug) }}" class="product-card">
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
        <div class="products-index-pagination">
            {{ $products->links() }}
        </div>
    @endif
</div>

@endsection