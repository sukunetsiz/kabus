<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class VendorsController extends Controller
{
    /**
     * Display a listing of vendors.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            $vendors = User::whereHas('roles', function($query) {
                $query->where('name', 'vendor');
            })
            ->with('profile') // Include profile relationship
            ->get(['id', 'username']); // Only get necessary fields

            return view('vendors.index', compact('vendors'));
        } catch (Exception $e) {
            Log::error('Error fetching vendors list: ' . $e->getMessage());
            return view('vendors.index', ['vendors' => collect()])->with('error', 'Unable to load vendors at this time.');
        }
    }

    /**
     * Display the specified vendor.
     *
     * @param  string  $username
     * @return \Illuminate\View\View
     */
    public function show($username)
    {
        try {
            // Get vendor with their profile and products
            $vendor = User::whereHas('roles', function($query) {
                $query->where('name', 'vendor');
            })
            ->with(['vendorProfile', 'profile', 'pgpKey']) // Include profiles and pgp key
            ->where('username', $username)
            ->firstOrFail(['id', 'username']);

            // Check if vendor is in vacation mode
            if ($vendor->vendorProfile && $vendor->vendorProfile->vacation_mode) {
                return view('vendors.show', [
                    'vendor' => $vendor,
                    'vacation_mode' => true,
                    'products' => collect() // Empty collection when in vacation mode
                ]);
            }

            // Get vendor's active products
            $products = Product::where('user_id', $vendor->id)
                ->active()
                ->latest()
                ->paginate(8);

            return view('vendors.show', [
                'vendor' => $vendor,
                'vacation_mode' => false,
                'products' => $products
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching vendor details: ' . $e->getMessage(), ['username' => $username]);
            return redirect()->route('vendors.index')->with('error', 'Vendor not found or unavailable.');
        }
    }
}
