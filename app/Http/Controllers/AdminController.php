<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Role;
use App\Models\BannedUser;
use App\Models\SupportRequest;
use App\Models\SupportMessage;
use App\Models\Notification;
use App\Models\Category;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    public function showUpdateStatus()
    {
        $currentStatus = Storage::get('public/kabus_current_status.txt');
        return view('admin.update-status', compact('currentStatus'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|string|max:3200',
        ]);

        Storage::put('public/kabus_current_status.txt', $request->status);

        return redirect()->route('admin.update-status')->with('success', 'Status updated successfully.');
    }

    public function showLogs()
    {
        return view('admin.logs.index');
    }

    private function getFilteredLogs($logTypes)
    {
        $logPath = storage_path('logs/laravel.log');
        $logs = [];

        if (File::exists($logPath)) {
            $content = File::get($logPath);
            $pattern = '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.*?)(\n|\z)/s';
            preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $logType = strtolower($match[3]);
                if (in_array($logType, $logTypes)) {
                    $logs[] = [
                        'datetime' => $match[1],
                        'type' => $match[3],
                        'message' => $match[4],
                    ];
                }
            }
        }

        return array_reverse($logs);
    }

    public function showErrorLogs()
    {
        $logs = $this->getFilteredLogs(['error', 'critical', 'alert', 'emergency']);
        return view('admin.logs.error', compact('logs'));
    }

    public function showWarningLogs()
    {
        $logs = $this->getFilteredLogs(['warning', 'notice']);
        return view('admin.logs.warning', compact('logs'));
    }

    public function showInfoLogs()
    {
        $logs = $this->getFilteredLogs(['info', 'debug']);
        return view('admin.logs.info', compact('logs'));
    }

    public function deleteLogs($type)
    {
        $logPath = storage_path('logs/laravel.log');
        
        if (File::exists($logPath)) {
            $content = File::get($logPath);
            $pattern = '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.*?)(\n|\z)/s';
            preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

            $newContent = '';
            foreach ($matches as $match) {
                $logType = strtolower($match[3]);
                if ($type === 'error' && !in_array($logType, ['error', 'critical', 'alert', 'emergency'])) {
                    $newContent .= $match[0];
                } elseif ($type === 'warning' && !in_array($logType, ['warning', 'notice'])) {
                    $newContent .= $match[0];
                } elseif ($type === 'info' && !in_array($logType, ['info', 'debug'])) {
                    $newContent .= $match[0];
                }
            }

            File::put($logPath, $newContent);
        }

        return redirect()->route('admin.logs')->with('success', ucfirst($type) . ' logs deleted successfully.');
    }

    public function userList()
    {
        $users = User::orderBy('username')->paginate(32);
        return view('admin.users.list', compact('users'));
    }

    public function userDetails(User $user)
    {
        return view('admin.users.details', compact('user'));
    }

    public function updateUserRoles(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'array',
            'roles.*' => 'in:admin,vendor',
        ]);

        $roles = $request->input('roles', []);

        // Sync the user's roles
        $roleIds = Role::whereIn('name', $roles)->pluck('id');
        $user->roles()->sync($roleIds);

        return redirect()->route('admin.users.details', $user)
            ->with('success', 'User roles updated successfully.');
    }

    public function banUser(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
        ]);

        $bannedUntil = now()->addDays((int)$request->duration);

        BannedUser::updateOrCreate(
            ['user_id' => $user->id],
            [
                'reason' => $request->reason,
                'banned_until' => $bannedUntil,
            ]
        );

        // Log the ban action
        Log::info("User banned: ID {$user->id} banned until {$bannedUntil} for reason: {$request->reason}");

        return redirect()->route('admin.users.details', $user)
            ->with('success', 'User banned successfully.');
    }

    public function unbanUser(User $user)
    {
        $user->bannedUser()->delete();

        // Log the unban action
        Log::info("User unbanned: ID {$user->id}");

        return redirect()->route('admin.users.details', $user)
            ->with('success', 'User unbanned successfully.');
    }

    public function supportRequests()
    {
        $requests = SupportRequest::with(['user', 'latestMessage'])
            ->orderBy('created_at', 'desc')
            ->paginate(16);

        return view('admin.support.index', compact('requests'));
    }

    public function showSupportRequest(SupportRequest $supportRequest)
    {
        $messages = $supportRequest->messages()->with('user')->get();
        
        return view('admin.support.show', compact('supportRequest', 'messages'));
    }

    public function replySupportRequest(Request $request, SupportRequest $supportRequest)
    {
        // Add status validation check
        if ($supportRequest->status === 'closed') {
            return redirect()->route('admin.support.show', $supportRequest->ticket_id)
                ->with('error', 'Cannot reply to a closed ticket. Please update the ticket status to open or in progress first.');
        }

        $request->validate([
            'message' => 'required|string|max:5000'
        ]);

        // Create the admin reply
        SupportMessage::create([
            'support_request_id' => $supportRequest->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
            'is_admin_reply' => true
        ]);

        // Update support request status if needed
        if ($supportRequest->status === 'open') {
            $supportRequest->update(['status' => 'in_progress']);
        }

        return redirect()->route('admin.support.show', $supportRequest->ticket_id)
            ->with('success', 'Reply sent successfully.');
    }

    public function updateSupportStatus(Request $request, SupportRequest $supportRequest)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,closed'
        ]);

        $oldStatus = $supportRequest->status;
        $supportRequest->update(['status' => $request->status]);

        // Only log when ticket is closed
        if ($request->status === 'closed') {
            Log::info("Support ticket {$supportRequest->ticket_id} closed by admin {$request->user()->id}");
        }

        return redirect()->route('admin.support.show', $supportRequest->ticket_id)
            ->with('success', 'Status updated successfully.');
    }

    // Bulk Messaging Methods
    public function showBulkMessage()
    {
        $roles = Role::all();
        return view('admin.bulk-message.create', compact('roles'));
    }

    public function sendBulkMessage(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|min:3|max:255',
                'message' => 'required|string|min:10|max:5000',
                'target_role' => 'nullable|string|in:admin,vendor',
            ]);

            // Strip any malicious HTML but keep basic formatting
            $sanitizedMessage = strip_tags($request->message, '<p><br><strong><em><ul><li><ol>');
            $sanitizedTitle = strip_tags($request->title);

            // Verify target role exists if specified
            if ($request->target_role) {
                $roleExists = Role::where('name', $request->target_role)->exists();
                if (!$roleExists) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Specified user group not found.');
                }
            }

            $notification = Notification::create([
                'title' => $sanitizedTitle,
                'message' => $sanitizedMessage,
                'target_role' => $request->target_role,
            ]);

            $notification->sendToTargetUsers();

            Log::info("Bulk message sent by admin {$request->user()->id} to " . 
                     ($request->target_role ?? 'all users'));

            return redirect()->route('admin.bulk-message.list')
                ->with('success', 'Bulk message sent successfully.');

        } catch (\Exception $e) {
            Log::error('Error sending bulk message: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while sending the message. Please try again later.');
        }
    }

    public function listBulkMessages()
    {
        try {
            $notifications = Notification::orderBy('created_at', 'desc')
                ->withCount('users')
                ->paginate(16);

            // Add translated role names to each notification
            foreach ($notifications as $notification) {
                if ($notification->target_role) {
                    $roleTranslations = [
                        'vendor' => 'Vendor',
                        'admin' => 'Administrator'
                    ];
                    $notification->translated_role = $roleTranslations[$notification->target_role] ?? ucfirst($notification->target_role);
                }
            }

            return view('admin.bulk-message.list', compact('notifications'));
        } catch (\Exception $e) {
            Log::error('Error listing bulk messages: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while listing messages. Please try again later.');
        }
    }

    public function deleteBulkMessage(Notification $notification)
    {
        try {
            $notification->delete();
            return redirect()->route('admin.bulk-message.list')
                ->with('success', 'Bulk message deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting bulk message: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while deleting the message. Please try again later.');
        }
    }

    // Category Management Methods
    public function categories()
    {
        $mainCategories = Category::mainCategories();
        return view('admin.categories', compact('mainCategories'));
    }

    public function storeCategory(Request $request)
    {
        try {
            $request->validate(Category::validationRules());

            $category = Category::create([
                'name' => $request->name,
                'parent_id' => $request->parent_id
            ]);

            Log::info("Category created: {$category->getFormattedName()} by admin {$request->user()->id}");

            return redirect()->route('admin.categories')
                ->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating category: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating category. Please try again.');
        }
    }

    public function deleteCategory(Category $category)
    {
        try {
            $categoryName = $category->getFormattedName();
            $category->delete();

            Log::info("Category deleted: {$categoryName} by admin " . auth()->id());

            return redirect()->route('admin.categories')
                ->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting category: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error deleting category. Please try again.');
        }
    }

    public function listCategories(Request $request)
    {
        // Only allow AJAX requests
        if (!$request->ajax()) {
            abort(404);
        }

        try {
            $categories = Category::with('parent')->get()->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->getFormattedName()
                ];
            });

            return response()->json($categories);
        } catch (\Exception $e) {
            Log::error('Error listing categories: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching categories'], 500);
        }
    }
}
