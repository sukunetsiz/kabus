<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdatePgpKeyRequest;
use App\Models\PgpKey;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Database\QueryException;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('settings', compact('user'));
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = Auth::user();

        try {
            $user->password = Hash::make($request->password);
            $user->save();

            // Log password change
            Log::info("User {$user->id} changed their password");

            // Regenerate the session token
            $request->session()->regenerate();

            return back()->with('status', 'Password successfully changed. Your session has been renewed for security purposes.');
        } catch (QueryException $e) {
            Log::error("Error changing password for user {$user->id}: " . $e->getMessage());
            return back()->with('error', 'An error occurred while changing your password. Please try again or contact support if the problem persists.');
        } catch (Exception $e) {
            Log::error("Unexpected error changing password for user {$user->id}: " . $e->getMessage());
            return back()->with('error', 'An unexpected error occurred. Please try again or contact support if the problem persists.');
        }
    }

    public function updatePgpKey(UpdatePgpKeyRequest $request)
    {
        $user = Auth::user();
        $pgpKey = $user->pgpKey ?? new PgpKey();

        try {
            $pgpKey->user_id = $user->id;
            $pgpKey->public_key = $request->public_key;
            $pgpKey->verified = false; // Set verified to false when updating the PGP key
            $pgpKey->save();

            // Log PGP key update
            Log::info("User {$user->id} updated their PGP key");

            return back()->with('status', 'PGP key successfully updated. Please verify your new PGP key.');
        } catch (QueryException $e) {
            Log::error("Error updating PGP key for user {$user->id}: " . $e->getMessage());
            return back()->with('error', 'An error occurred while updating your PGP key. Please try again or contact support if the problem persists.');
        } catch (Exception $e) {
            Log::error("Unexpected error updating PGP key for user {$user->id}: " . $e->getMessage());
            return back()->with('error', 'An unexpected error occurred. Please try again or contact support if the problem persists.');
        }
    }
}
