<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use App\Models\Notification;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (RateLimiter::tooManyAttempts('send-message:'.Auth::id(), $perMinute = 5)) {
                Log::warning('Rate limit exceeded for user: ' . Auth::id());
                return response()->view('messages.rate-limit', [], 429);
            }

            RateLimiter::hit('send-message:'.Auth::id());

            return $next($request);
        })->only(['store', 'startConversation']);
    }

    public function index()
    {
        $user = Auth::user();
        $conversations = $user->conversations()->with(['user1', 'user2'])->orderBy('last_message_at', 'desc')->paginate(4);
        return view('messages.index', compact('conversations'));
    }

    public function show($id)
    {
        $conversation = Message::conversation()->findOrFail($id);

        if (RateLimiter::tooManyAttempts('view-conversation:'.Auth::id(), $perMinute = 60)) {
            return response()->view('messages.rate-limit', [], 429);
        }
        RateLimiter::hit('view-conversation:'.Auth::id());

        $this->authorize('view', $conversation);
        $messages = $conversation->messages()->with('sender')->orderBy('created_at', 'asc')->paginate(40);
        
        // Mark unread messages as read
        $unreadMessages = $messages->where('is_read', false)->where('sender_id', '!=', Auth::id());
        foreach ($unreadMessages as $message) {
            $message->is_read = true;
            $message->save();
        }
        
        return view('messages.show', compact('conversation', 'messages'));
    }

    public function store(Request $request, $id)
    {
        $conversation = Message::conversation()->findOrFail($id);
        $this->authorize('sendMessage', $conversation);

        // Check if the conversation has reached the 40-message limit
        if ($conversation->hasReachedMessageLimit()) {
            return redirect()->back()->with('error', 'Message limit of 40 has been reached for this chat. Please delete this chat and start a new one with the user.');
        }

        $validatedData = $request->validate([
            'content' => 'required|string|min:4|max:1600',
        ]);

        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'p,b,i,u,a[href],ul,ol,li'); // Restrict allowed HTML tags
        $purifier = new HTMLPurifier($config);
        $cleanContent = $purifier->purify($validatedData['content']);

        $message = new Message([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'content' => $cleanContent,
        ]);

        if (!$message->save()) {
            Log::error('Failed to save message', ['user_id' => Auth::id(), 'conversation_id' => $conversation->id]);
            return redirect()->back()->with('error', 'Failed to send message. Please try again.');
        }

        $conversation->last_message_at = now();
        $conversation->save();

        // Create notification for the recipient
        $recipientId = $conversation->user_id_1 == Auth::id() ? $conversation->user_id_2 : $conversation->user_id_1;
        $recipient = User::find($recipientId);
        
        // Check if there's already an unread notification from this sender
        $existingNotification = $recipient->notifications()
            ->where('title', 'LIKE', 'New message from ' . Auth::user()->username)
            ->wherePivot('read', false)
            ->first();

        if (!$existingNotification) {
            // Create a new notification
            $notification = new Notification([
                'title' => 'New message from ' . Auth::user()->username,
                'message' => 'You have received a new message from ' . Auth::user()->username,
                'type' => 'message',
            ]);
            $notification->save();
            $notification->users()->attach($recipientId, ['read' => false]);
        }

        return redirect()->route('messages.show', $conversation);
    }

    public function create(Request $request)
    {
        $username = $request->query('username');
        
        if (Auth::user()->hasReachedConversationLimit()) {
            return redirect()->route('messages.index')->with('error', 'Conversation limit of 16 reached. Please delete other conversations to create a new one.');
        }
        
        return view('messages.create', ['username' => $username]);
    }

    public function startConversation(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required|string|alpha_num|max:16',
            'content' => 'required|string|min:4|max:1600',
        ]);

        if ($validatedData['username'] === Auth::user()->username) {
            return redirect()->back()->with('error', 'You cannot send messages to yourself.')->withInput();
        }

        $otherUser = User::where('username', $validatedData['username'])->first();

        if (!$otherUser) {
            return redirect()->back()->with('error', 'The specified user does not exist.')->withInput();
        }

        if ($otherUser->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot start a chat with yourself.')->withInput();
        }

        $existingConversation = Message::findConversation(Auth::id(), $otherUser->id);

        if ($existingConversation) {
            $conversation = $existingConversation;
            
            // Check if the existing conversation has reached the 40-message limit
            if ($conversation->hasReachedMessageLimit()) {
                return redirect()->back()->with('error', 'Message limit of 40 has been reached for the existing chat. Please delete it and start a new one.');
            }
        } else {
            // Check if the user has reached the conversation limit
            if (Auth::user()->hasReachedConversationLimit()) {
                return redirect()->back()->with('error', 'Chat limit of 16 has been reached. Please delete other chats to create a new one.');
            }

            $conversation = Message::createConversation(Auth::id(), $otherUser->id);
        }

        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'p,b,i,u,a[href],ul,ol,li');
        $purifier = new HTMLPurifier($config);
        $cleanContent = $purifier->purify($validatedData['content']);

        $message = new Message([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'content' => $cleanContent,
        ]);

        if (!$message->save()) {
            Log::error('Failed to save initial message', ['user_id' => Auth::id(), 'with_user_id' => $otherUser->id]);
            return redirect()->back()->with('error', 'Failed to start chat. Please try again.');
        }

        $conversation->last_message_at = now();
        $conversation->save();

        // Create notification for new conversation
        $notification = new Notification([
            'title' => 'New message from ' . Auth::user()->username,
            'message' => 'You have received a new message from ' . Auth::user()->username,
            'type' => 'message',
        ]);
        $notification->save();
        $notification->users()->attach($otherUser->id, ['read' => false]);

        return redirect()->route('messages.show', $conversation);
    }

    public function destroy($id)
    {
        $conversation = Message::conversation()->findOrFail($id);
        $this->authorize('delete', $conversation);

        try {
            $conversation->delete(); // This will perform a soft delete
            return redirect()->route('messages.index')->with('success', 'Chat successfully deleted.');
        } catch (\Exception $e) {
            Log::error('Failed to delete conversation', ['user_id' => Auth::id(), 'conversation_id' => $conversation->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to delete chat. Please try again.');
        }
    }
}
