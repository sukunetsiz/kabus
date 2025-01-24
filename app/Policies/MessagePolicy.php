<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class MessagePolicy
{
    /**
     * Determine whether the user can view any conversations.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view their conversations
    }

    /**
     * Determine whether the user can view the conversation or message.
     */
    public function view(User $user, Message $message): bool
    {
        $canView = $user->id === $message->user_id_1 || $user->id === $message->user_id_2;
        
        if (!$canView) {
            Log::warning('Unauthorized conversation access attempt', [
                'user_id' => $user->id,
                'message_id' => $message->id
            ]);
        }
        
        return $canView;
    }

    /**
     * Determine whether the user can create conversations.
     */
    public function create(User $user): bool
    {
        // Add any additional checks here if needed
        return true;
    }

    /**
     * Determine whether the user can send a message in the conversation.
     */
    public function sendMessage(User $user, Message $message): bool
    {
        $canSend = $user->id === $message->user_id_1 || $user->id === $message->user_id_2;
        
        if (!$canSend) {
            Log::warning('Unauthorized message send attempt', [
                'user_id' => $user->id,
                'message_id' => $message->id
            ]);
        }
        
        return $canSend;
    }

    /**
     * Determine whether the user can delete the conversation.
     */
    public function delete(User $user, Message $message): bool
    {
        $canDelete = $user->id === $message->user_id_1 || $user->id === $message->user_id_2;
        
        if (!$canDelete) {
            Log::warning('Unauthorized conversation delete attempt', [
                'user_id' => $user->id,
                'message_id' => $message->id
            ]);
        }
        
        return $canDelete;
    }

    /**
     * Determine whether the user can permanently delete the conversation.
     */
    public function forceDelete(User $user, Message $message): bool
    {
        return false; // Disallow permanent deletion for data retention purposes
    }

    /**
     * Determine whether the user can restore the conversation.
     */
    public function restore(User $user, Message $message): bool
    {
        $canRestore = $user->id === $message->user_id_1 || $user->id === $message->user_id_2;
        
        if (!$canRestore) {
            Log::warning('Unauthorized conversation restore attempt', [
                'user_id' => $user->id,
                'message_id' => $message->id
            ]);
        }
        
        return $canRestore;
    }
}