<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

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
        
        return view('references', compact('referenceId', 'usedReferenceCode', 'referrerUsername', 'referrals'));
    }
}
