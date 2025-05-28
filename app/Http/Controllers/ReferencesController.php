<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VendorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReferencesController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $referenceId = $user->reference_id;
        
        // Get referrer information if it exists
        $referrer = $user->referrer;
        $usedReferenceCode = $referrer !== null;
        $referrerUsername = $usedReferenceCode ? $referrer->username : null;
        
        // Get users who used this user's reference code
        $referrals = $user->referrals;
        
        // Get saved vendor reference IDs
        $privateShops = DB::table('private_shops')
            ->where('user_id', $user->id)
            ->join('users', 'private_shops.vendor_id', '=', 'users.id')
            ->select('private_shops.id', 'private_shops.vendor_reference_id', 'users.username as vendor_username')
            ->get();
        
        return view('references', compact(
            'referenceId', 
            'usedReferenceCode', 
            'referrerUsername', 
            'referrals',
            'privateShops'
        ));
    }
    
    /**
     * Store a vendor reference ID
     */
    public function storeVendorReference(Request $request)
    {
        $validated = $request->validate([
            'vendor_reference_id' => 'required|string',
        ]);
        
        $user = Auth::user();
        $vendorReferenceId = $validated['vendor_reference_id'];
        
        // Find the vendor by reference ID
        $vendor = User::whereHas('roles', function($query) {
            $query->where('name', 'vendor');
        })->get()->filter(function($user) use ($vendorReferenceId) {
            return $user->reference_id === $vendorReferenceId;
        })->first();
        
        if (!$vendor) {
            return redirect()->back()->with('error', 'Invalid vendor reference ID or not a vendor account.');
        }
        
        // Check if already added
        $exists = DB::table('private_shops')
            ->where('user_id', $user->id)
            ->where('vendor_reference_id', $vendorReferenceId)
            ->exists();
            
        if ($exists) {
            return redirect()->back()->with('error', 'You have already added this vendor reference ID.');
        }
        
        // Store the vendor reference ID
        DB::table('private_shops')->insert([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
            'vendor_reference_id' => $vendorReferenceId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        return redirect()->back()->with('success', 'Vendor reference ID added successfully.');
    }
    
    /**
     * Remove a vendor reference ID
     */
    public function removeVendorReference($id)
    {
        $user = Auth::user();
        
        DB::table('private_shops')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->delete();
            
        return redirect()->back()->with('success', 'Vendor reference ID removed successfully.');
    }
}
