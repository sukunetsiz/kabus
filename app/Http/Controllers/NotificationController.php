<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * Display a listing of the user's notifications.
     */
    public function index()
    {
        try {
            $user = auth()->user();
            $notifications = $user->notifications()
                ->orderBy('created_at', 'desc')
                ->paginate(16);

            return view('notifications.index', compact('notifications'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while loading notifications. Please try again later.');
        }
    }
}
