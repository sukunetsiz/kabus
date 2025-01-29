<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VendorProfile;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;

class VendorController extends Controller
{
    /**
     * Display the vendor dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('vendor.index');
    }

    /**
     * Show the form for editing vendor appearance.
     *
     * @return \Illuminate\View\View
     */
    public function showAppearance()
    {
        $user = Auth::user();
        $vendorProfile = $user->vendorProfile ?? new VendorProfile();
        
        return view('vendor.appearance', compact('vendorProfile'));
    }

    /**
     * Update the vendor's appearance settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAppearance(Request $request)
    {
        $request->validate([
            'description' => 'required|string|min:8|max:800',
            'vacation_mode' => 'required|in:0,1',
        ], [
            'description.required' => 'A description is required.',
            'description.min' => 'Description must be at least 8 characters.',
            'description.max' => 'Description cannot exceed 800 characters.',
            'vacation_mode.in' => 'Invalid vacation mode value.'
        ]);

        $user = Auth::user();
        $vendorProfile = $user->vendorProfile ?? new VendorProfile();
        
        if (!$user->vendorProfile) {
            $vendorProfile->user_id = $user->id;
        }
        
        $vendorProfile->description = $request->description;
        $vendorProfile->vacation_mode = (bool) $request->vacation_mode;
        $vendorProfile->save();

        return redirect()->route('vendor.appearance')
            ->with('success', 'Vendor settings updated successfully.');
    }

    /**
     * Display the vendor's products.
     *
     * @return \Illuminate\View\View
     */
    public function myProducts()
    {
        $products = Product::where('user_id', Auth::id())
            ->select('id', 'name', 'type', 'slug')
            ->get();

        return view('vendor.my-products', compact('products'));
    }

    /**
     * Delete a product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product)
    {
        // Check if the authenticated user owns this product
        if ($product->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $product->delete();

        return redirect()->route('vendor.my-products')
            ->with('success', 'Product deleted successfully.');
    }
}
