<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\XmrPriceController;

class OrdersController extends Controller
{
    /**
     * Display a listing of the user's orders.
     */
    public function index()
    {
        $orders = Orders::getUserOrders(Auth::id());
        
        return view('orders.index', [
            'orders' => $orders
        ]);
    }

    /**
     * Display the specified order.
     */
    public function show($uniqueUrl)
    {
        $order = Orders::findByUrl($uniqueUrl);
        
        if (!$order) {
            abort(404);
        }

        // Check if the user is either the buyer or the vendor
        if ($order->user_id !== Auth::id() && $order->vendor_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        // Determine if the current user is the buyer or vendor
        $isBuyer = $order->user_id === Auth::id();
        
        return view('orders.show', [
            'order' => $order,
            'isBuyer' => $isBuyer
        ]);
    }

    /**
     * Create a new order from the cart items.
     */
    public function store(Request $request, XmrPriceController $xmrPriceController)
    {
        try {
            $user = Auth::user();
            $cartItems = Cart::where('user_id', $user->id)->with(['product', 'product.user'])->get();
            
            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
            }
            
            // Calculate order totals
            $subtotal = Cart::getCartTotal($user);
            $commissionPercentage = config('marketplace.commission_percentage');
            $commission = ($subtotal * $commissionPercentage) / 100;
            $total = $subtotal + $commission;
            
            // Create the order
            $order = Orders::createFromCart($user, $cartItems, $subtotal, $commission, $total);
            
            // Clear the cart
            Cart::where('user_id', $user->id)->delete();
            
            return redirect()->route('orders.show', $order->unique_url)
                ->with('success', 'Order created successfully.');
                
        } catch (\Exception $e) {
            return redirect()->route('cart.checkout')
                ->with('error', 'Failed to create order. Please try again.');
        }
    }

    /**
     * Mark the order as paid.
     */
    public function markAsPaid($uniqueUrl)
    {
        $order = Orders::findByUrl($uniqueUrl);
        
        if (!$order) {
            abort(404);
        }

        // Verify ownership - only the buyer can mark as paid
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($order->markAsPaid()) {
            return redirect()->route('orders.show', $order->unique_url)
                ->with('success', 'Payment confirmed. The vendor has been notified.');
        }

        return redirect()->route('orders.show', $order->unique_url)
            ->with('error', 'Unable to confirm payment at this time.');
    }

    /**
     * Mark the order as delivered.
     */
    public function markAsDelivered($uniqueUrl)
    {
        $order = Orders::findByUrl($uniqueUrl);
        
        if (!$order) {
            abort(404);
        }

        // Verify ownership - only the vendor can mark as delivered
        if ($order->vendor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($order->markAsDelivered()) {
            return redirect()->route('vendor.sales.show', $order->unique_url)
                ->with('success', 'Product marked as delivered. The buyer has been notified.');
        }

        return redirect()->route('vendor.sales.show', $order->unique_url)
            ->with('error', 'Unable to mark as delivered at this time.');
    }

    /**
     * Mark the order as completed.
     */
    public function markAsCompleted($uniqueUrl)
    {
        $order = Orders::findByUrl($uniqueUrl);
        
        if (!$order) {
            abort(404);
        }

        // Verify ownership - only the buyer can mark as completed
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($order->markAsCompleted()) {
            return redirect()->route('orders.show', $order->unique_url)
                ->with('success', 'Order marked as completed. Thank you for your purchase.');
        }

        return redirect()->route('orders.show', $order->unique_url)
            ->with('error', 'Unable to mark as completed at this time.');
    }

    /**
     * Mark the order as cancelled.
     */
    public function markAsCancelled($uniqueUrl)
    {
        $order = Orders::findByUrl($uniqueUrl);
        
        if (!$order) {
            abort(404);
        }

        // Verify ownership - both buyer and vendor can cancel
        if ($order->user_id !== Auth::id() && $order->vendor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if order is in a status that can be cancelled
        if ($order->status === Orders::STATUS_COMPLETED) {
            return redirect()->back()->with('error', 'Completed orders cannot be cancelled.');
        }

        if ($order->markAsCancelled()) {
            // Determine the redirect route based on whether the user is buyer or vendor
            $isBuyer = $order->user_id === Auth::id();
            $route = $isBuyer ? 'orders.show' : 'vendor.sales.show';
            
            return redirect()->route($route, $order->unique_url)
                ->with('success', 'Order has been cancelled successfully.');
        }

        return redirect()->back()->with('error', 'Unable to cancel the order at this time.');
    }
}