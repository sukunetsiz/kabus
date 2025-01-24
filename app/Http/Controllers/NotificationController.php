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

            return view('notifications', compact('notifications'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while loading notifications. Please try again later.');
        }
    }

    /**
     * Mark the specified notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        try {
            $user = auth()->user();
            
            // Check if the notification belongs to the user
            $userNotification = $user->notifications()
                ->where('notifications.id', $notification->id)
                ->first();

            if (!$userNotification) {
                return redirect()->route('notifications.index')
                    ->with('error', 'Notification not found.');
            }

            // Update the pivot table directly
            $user->notifications()->updateExistingPivot($notification->id, ['read' => true]);

            return redirect()->route('notifications.index')
                ->with('success', 'Notification marked as read.');
        } catch (\Exception $e) {
            \Log::error('Failed to mark notification as read: ' . $e->getMessage());
            return redirect()->route('notifications.index')
                ->with('error', 'Failed to mark notification as read: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified notification from the user's list.
     */
    public function destroy(Notification $notification)
    {
        try {
            $user = auth()->user();
            
            // Check if the notification belongs to the user
            $userNotification = $user->notifications()
                ->where('notifications.id', $notification->id)
                ->first();

            if (!$userNotification) {
                return redirect()->route('notifications.index')
                    ->with('error', 'Notification not found.');
            }

            // Detach the notification from the user
            $user->notifications()->detach($notification->id);

            return redirect()->route('notifications.index')
                ->with('success', 'Notification deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to delete notification: ' . $e->getMessage());
            return redirect()->route('notifications.index')
                ->with('error', 'Failed to delete notification: ' . $e->getMessage());
        }
    }
}
