<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Referral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReferencesController extends Controller
{
    const MAX_REFERRALS = 160; // Maximum number of referrals a user can add

    public function index()
    {
        $user = Auth::user();
        $referenceId = $user->reference_id;
        $referrals = $user->referrals()->with('referredUser:id,username')->get();
        
        // Get the count of users who have used this user's reference ID
        $referralCount = Referral::where('referred_user_id', $user->id)->count();
        
        // Get the list of users who have used this user's reference ID
        $referredByUsers = Referral::where('referred_user_id', $user->id)
            ->with('user:id,username')
            ->get()
            ->pluck('user');
        
        return view('references', compact('referenceId', 'referrals', 'referralCount', 'referredByUsers'));
    }

    public function addReferral(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'reference_id' => ['required', 'string', 'size:16'],
        ], [
            'reference_id.size' => 'Reference ID must be 16 characters long.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('references.index')
                ->withErrors($validator)
                ->withInput();
        }

        $referenceId = $request->input('reference_id');

        // Check if the user has reached the maximum number of referrals
        if ($user->referrals()->count() >= self::MAX_REFERRALS) {
            return redirect()->route('references.index')
                ->withErrors(['error' => 'You have reached the maximum number of references.']);
        }

        // Use a timing-safe comparison
        if (Hash::check($referenceId, Hash::make($user->reference_id))) {
            Log::warning('User attempted to add own reference ID', ['user_id' => $user->id]);
            return redirect()->route('references.index')
                ->withErrors(['reference_id' => 'You cannot add your own reference ID.']);
        }

        // Check if the reference ID exists
        $referredUser = User::all()->first(function ($u) use ($referenceId) {
            return $u->reference_id === $referenceId;
        });

        if (!$referredUser) {
            Log::warning('Invalid reference attempt', ['user_id' => $user->id, 'reference_id' => $referenceId]);
            return redirect()->route('references.index')
                ->withErrors(['reference_id' => 'Invalid reference ID.']);
        }

        // Check if the reference ID has already been added
        $encryptedReferenceId = Crypt::encryptString($referenceId);
        if ($user->referrals()->where('referred_user_id', $referredUser->id)->exists() ||
            $user->referrals()->where('referred_user_reference_id', $encryptedReferenceId)->exists()) {
            return redirect()->route('references.index')
                ->withErrors(['reference_id' => 'This reference ID has already been added.']);
        }

        // Create the referral
        try {
            DB::beginTransaction();
            
            $user->referrals()->create([
                'referred_user_reference_id' => $referenceId,
                'referred_user_id' => $referredUser->id,
            ]);
            
            DB::commit();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            // Check if the error is due to a unique constraint violation
            if ($e->errorInfo[1] == 1062) {
                return redirect()->route('references.index')
                    ->withErrors(['reference_id' => 'This reference ID has already been added.']);
            }
            Log::error('Error adding referral', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return redirect()->route('references.index')
                ->withErrors(['error' => 'An error occurred while adding the reference. Please try again later.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Unexpected error adding referral', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return redirect()->route('references.index')
                ->withErrors(['error' => 'An error occurred while adding the reference. Please try again later.']);
        }

        return redirect()->route('references.index')
            ->with('success', 'Reference ID successfully added.');
    }
}
