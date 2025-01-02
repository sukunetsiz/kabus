<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Display the user's wishlist.
     */
    public function index()
    {
        try {
            $wishlistedProducts = Auth::user()->wishlist()
                ->with(['user:id,username', 'category:id,name'])
                ->paginate(12);

            return view('wishlist', [
                'products' => $wishlistedProducts,
                'title' => 'My Wishlist'
            ]);
        } catch (\Exception $e) {
            return redirect()
                ->route('products.index')
                ->with('error', 'An error occurred while loading your wishlist.');
        }
    }

    /**
     * Add a product to the user's wishlist.
     */
    public function store(Product $product)
    {
        try {
            // Check if product is active
            if (!$product->active) {
                return back()->with('error', 'This product is no longer available.');
            }

            // Check if product is already in wishlist
            if (Auth::user()->hasWishlisted($product->id)) {
                return back()->with('error', 'This product is already in your wishlist.');
            }

            // Add to wishlist
            Auth::user()->wishlist()->attach($product->id);

            return back()->with('success', 'Product added to your wishlist.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while adding the product to your wishlist.');
        }
    }

    /**
     * Remove a product from the user's wishlist.
     */
    public function destroy(Product $product)
    {
        try {
            Auth::user()->wishlist()->detach($product->id);
            return back()->with('success', 'Product removed from your wishlist.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while removing the product from your wishlist.');
        }
    }

    /**
     * Clear all products from the user's wishlist.
     */
    public function clearAll()
    {
        try {
            Auth::user()->wishlist()->detach();
            return back()->with('success', 'Your wishlist has been cleared.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while clearing your wishlist.');
        }
    }
}
