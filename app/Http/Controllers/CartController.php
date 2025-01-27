<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\XmrPriceController;

class CartController extends Controller
{
    /**
     * Display the user's cart.
     */
    public function index(XmrPriceController $xmrPriceController)
    {
        $cartItems = Cart::where('user_id', Auth::id())
            ->with(['product', 'product.user'])
            ->get();

        $xmrPrice = $xmrPriceController->getXmrPrice();
        $cartTotal = Cart::getCartTotal(Auth::user());
        $xmrTotal = is_numeric($xmrPrice) && $xmrPrice > 0 
            ? $cartTotal / $xmrPrice 
            : null;

        return view('cart.index', [
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'xmrTotal' => $xmrTotal,
            'xmrPrice' => $xmrPrice
        ]);
    }

    /**
     * Add a product to cart.
     */
    public function store(Request $request, Product $product)
    {
        try {
            $validated = $request->validate([
                'quantity' => 'required|integer|min:1',
                'delivery_option' => 'required|integer|min:0',
                'bulk_option' => 'nullable|integer|min:0'
            ]);

            // Validate product addition
            $validation = Cart::validateProductAddition(Auth::user(), $product);
            if (!$validation['valid']) {
                return back()->with('error', $validation['message']);
            }

            // Get selected delivery option
            $deliveryOptions = $product->delivery_options;
            if (!isset($deliveryOptions[$validated['delivery_option']])) {
                return back()->with('error', 'Invalid delivery option selected.');
            }
            $selectedDelivery = $deliveryOptions[$validated['delivery_option']];

            // Handle bulk options and pricing
            $price = $product->price;
            $selectedBulk = null;
            $quantity = $validated['quantity'];

            if (isset($validated['bulk_option']) && $validated['bulk_option'] !== '' && $product->bulk_options) {
                if (!isset($product->bulk_options[$validated['bulk_option']])) {
                    return back()->with('error', 'Invalid bulk option selected.');
                }
                $selectedBulk = $product->bulk_options[$validated['bulk_option']];

                // Validate that quantity is a multiple of the bulk amount
                if ($quantity % $selectedBulk['amount'] !== 0) {
                    return back()->with('error', 'Quantity must be a multiple of ' . $selectedBulk['amount'] . ' when using bulk option.');
                }
                
                // For bulk options, we store the bulk price directly
                // When quantity is 12 and bulk option is "6 for $100",
                // we'll store $100 as the price, and quantity as 2 (sets)
                $price = $selectedBulk['price'];
                $quantity = $quantity / $selectedBulk['amount'];
            }

            // Create or update cart item
            Cart::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'product_id' => $product->id
                ],
                [
                    'quantity' => $quantity,
                    'price' => $price,
                    'selected_delivery_option' => $selectedDelivery,
                    'selected_bulk_option' => $selectedBulk
                ]
            );

            return redirect()
                ->route('cart.index')
                ->with('success', 'Product added to cart successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add product to cart.');
        }
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, Cart $cart)
    {
        try {
            // Verify ownership
            if ($cart->user_id !== Auth::id()) {
                abort(403);
            }

            $validated = $request->validate([
                'quantity' => 'required|integer|min:1'
            ]);

            // If this is a bulk item, validate the quantity
            if ($cart->selected_bulk_option) {
                // For bulk items, quantity represents number of sets
                $quantity = $validated['quantity'];
            } else {
                $quantity = $validated['quantity'];
            }

            $cart->update([
                'quantity' => $quantity
            ]);

            return redirect()
                ->route('cart.index')
                ->with('success', 'Cart updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update cart.');
        }
    }

    /**
     * Remove a specific item from cart.
     */
    public function destroy(Cart $cart)
    {
        try {
            // Verify ownership
            if ($cart->user_id !== Auth::id()) {
                abort(403);
            }

            $cart->delete();

            return redirect()
                ->route('cart.index')
                ->with('success', 'Item removed from cart successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to remove item from cart.');
        }
    }

    /**
     * Clear all items from cart.
     */
    public function clear()
    {
        try {
            Cart::where('user_id', Auth::id())->delete();

            return redirect()
                ->route('cart.index')
                ->with('success', 'Cart cleared successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to clear cart.');
        }
    }

    /**
     * Show checkout page (placeholder for future implementation).
     */
    public function checkout()
    {
        return view('cart.checkout', [
            'cartItems' => Cart::where('user_id', Auth::id())
                ->with(['product', 'product.user'])
                ->get()
        ]);
    }
}