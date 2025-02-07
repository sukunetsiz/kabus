@extends('layouts.app')
@section('content')

<div class="all-products-index-container">
    <div class="all-products-index-content">
        <div class="all-products-index-header">
            <h1 class="all-products-index-title">All Products</h1>
        </div>

        {{-- Error Messages --}}
        @if ($errors->any() || session('error'))
            <div class="all-products-index-alert">
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

        {{-- Search and Filter Form --}}
<div class="products-index-filter-card">
    <form action="{{ route('admin.all-products') }}" method="GET">
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
            <a href="{{ route('admin.all-products') }}" class="products-index-button products-index-button-secondary">
                Reset Filters
            </a>
            <button type="submit" class="products-index-button products-index-button-primary">
                Apply Filters
            </button>
        </div>
    </form>
</div>

        @if($products->isEmpty())
            <div class="all-products-index-empty">
                <p>No products found.</p>
            </div>
        @else
            <div class="all-products-index-table-container">
                <table class="all-products-index-table">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Type</th>
                            <th>Owner</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                               <td class="all-products-index-product-name">
                                    <a href="{{ route('products.show', $product->slug) }}" class="all-products-index-product-link">
                                        {{ \Str::limit($product->name, 60, '...') }}
                                    </a>
                               </td>
                                <td class="all-products-index-product-type">
                                    <span class="all-products-index-type-badge all-products-index-type-badge-{{ $product->type }}">
                                        {{ $product->type === 'deaddrop' ? 'Dead Drop' : ucfirst($product->type) }}
                                    </span>
                                </td>
                                <td class="all-products-index-product-owner">
                                    <span class="all-products-index-owner-badge">{{ $product->user->username }}</span>
                                </td>
                                <td class="all-products-index-actions">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="all-products-index-btn all-products-index-btn-edit">Edit</a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="all-products-index-btn all-products-index-btn-delete">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="all-products-index-pagination">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
