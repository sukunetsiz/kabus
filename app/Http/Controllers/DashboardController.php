<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;
use Exception;

class DashboardController extends Controller
{
    public function index($username = null)
    {
        try {
            $loggedInUser = Auth::user();
            
            if (!$loggedInUser) {
                Log::error('Unauthenticated user tried to access dashboard');
                return redirect()->route('login')->with('error', 'Please login to access the dashboard.');
            }

            if ($username) {
                $user = User::where('username', $username)->firstOrFail();
            } else {
                $user = $loggedInUser;
            }

            $profile = $user->profile;

            if (!$profile) {
                Log::info('Creating profile for user', ['user_id' => $user->id]);
                $profile = $user->profile()->create();
            }

            $pgpKey = $user->pgpKey;

            // Determine user role
            $userRole = $this->determineUserRole($user);

            $isOwnProfile = $user->id === $loggedInUser->id;

            // Determine what information to show based on user roles and ownership
            $showFullInfo = $isOwnProfile || $loggedInUser->isAdmin();

            // Decrypt the description if it exists, otherwise set a default message
            $description = $profile->description ? Crypt::decryptString($profile->description) : "This user hasn't added a description yet.";

            return view('dashboard', compact('user', 'profile', 'pgpKey', 'userRole', 'isOwnProfile', 'showFullInfo', 'description'));

        } catch (Exception $e) {
            Log::error('Error loading dashboard: ' . $e->getMessage(), ['user_id' => Auth::id()]);
            return redirect()->route('home')->with('error', 'An error occurred while loading the dashboard. Please try again.');
        }
    }

    private function determineUserRole(User $user): string
    {
        if ($user->hasRole('admin') && $user->hasRole('vendor')) {
            return 'Vendor Admin';
        } elseif ($user->hasRole('admin')) {
            return 'Administrator';
        } elseif ($user->hasRole('vendor')) {
            return 'Vendor';
        } else {
            return 'Buyer';
        }
    }
}