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
        <x-products 
            :products="$products"
        />
    @endif
</div>
@endsection

