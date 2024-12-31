<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
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
        $conversation = Conversation::findOrFail($id);

        if (RateLimiter::tooManyAttempts('view-conversation:'.Auth::id(), $perMinute = 60)) {
            return response()->view('messages.rate-limit', [], 429);
        }
        RateLimiter::hit('view-conversation:'.Auth::id());

        $this->authorize('view', $conversation);
        $messages = $conversation->messages()->with('sender')->orderBy('created_at', 'desc')->paginate(50);
        
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
        $conversation = Conversation::findOrFail($id);
        $this->authorize('sendMessage', $conversation);

        // Check if the conversation has reached the 40-message limit
        if ($conversation->hasReachedMessageLimit()) {
            return redirect()->back()->with('error', 'Message limit of 40 has been reached for this chat. Please delete this chat and start a new one with the user.');
        }

        $validatedData = $request->validate([
            'content' => 'required|string|min:1|max:1000',
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

        Log::info('Message sent', ['user_id' => Auth::id(), 'conversation_id' => $conversation->id]);

        return redirect()->route('messages.show', $conversation);
    }

    public function create()
    {
        return view('messages.create');
    }

    public function startConversation(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required|string|alpha_num|max:16',
            'content' => 'required|string|min:1|max:1000',
        ]);

        if ($validatedData['username'] === Auth::user()->username) {
            return redirect()->back()->withErrors(['username' => 'You cannot send messages to yourself.'])->withInput();
        }

        $otherUser = User::where('username', $validatedData['username'])->first();

        if (!$otherUser) {
            return redirect()->back()->withErrors(['username' => 'The specified user does not exist.'])->withInput();
        }

        if ($otherUser->id === Auth::id()) {
            return redirect()->back()->withErrors(['username' => 'You cannot start a chat with yourself.'])->withInput();
        }

        $existingConversation = Conversation::where(function ($query) use ($otherUser) {
            $query->where('user_id_1', Auth::id())->where('user_id_2', $otherUser->id);
        })->orWhere(function ($query) use ($otherUser) {
            $query->where('user_id_1', $otherUser->id)->where('user_id_2', Auth::id());
        })->first();

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

            $conversation = Conversation::create([
                'user_id_1' => Auth::id(),
                'user_id_2' => $otherUser->id,
                'last_message_at' => now(),
            ]);
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

        Log::info('Conversation started or continued', ['user_id' => Auth::id(), 'with_user_id' => $otherUser->id]);

        return redirect()->route('messages.show', $conversation);
    }

    public function destroy($id)
    {
        $conversation = Conversation::findOrFail($id);
        $this->authorize('delete', $conversation);

        try {
            $conversation->delete(); // This will perform a soft delete
            Log::info('Conversation soft deleted', ['user_id' => Auth::id(), 'conversation_id' => $conversation->id]);
            return redirect()->route('messages.index')->with('success', 'Chat successfully deleted.');
        } catch (\Exception $e) {
            Log::error('Failed to delete conversation', ['user_id' => Auth::id(), 'conversation_id' => $conversation->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to delete chat. Please try again.');
        }
    }
}
