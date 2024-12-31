<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class ConversationPolicy
{
    /**
     * Determine whether the user can view any conversations.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view their conversations
    }

    /**
     * Determine whether the user can view the conversation.
     */
    public function view(User $user, Conversation $conversation): bool
    {
        $canView = $user->id === $conversation->user_id_1 || $user->id === $conversation->user_id_2;
        
        if (!$canView) {
            Log::warning('Unauthorized conversation access attempt', [
                'user_id' => $user->id,
                'conversation_id' => $conversation->id
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
    public function sendMessage(User $user, Conversation $conversation): bool
    {
        $canSend = $user->id === $conversation->user_id_1 || $user->id === $conversation->user_id_2;
        
        if (!$canSend) {
            Log::warning('Unauthorized message send attempt', [
                'user_id' => $user->id,
                'conversation_id' => $conversation->id
            ]);
        }
        
        return $canSend;
    }

    /**
     * Determine whether the user can delete the conversation.
     */
    public function delete(User $user, Conversation $conversation): bool
    {
        $canDelete = $user->id === $conversation->user_id_1 || $user->id === $conversation->user_id_2;
        
        if (!$canDelete) {
            Log::warning('Unauthorized conversation delete attempt', [
                'user_id' => $user->id,
                'conversation_id' => $conversation->id
            ]);
        }
        
        return $canDelete;
    }

    /**
     * Determine whether the user can permanently delete the conversation.
     */
    public function forceDelete(User $user, Conversation $conversation): bool
    {
        return false; // Disallow permanent deletion for data retention purposes
    }

    /**
     * Determine whether the user can restore the conversation.
     */
    public function restore(User $user, Conversation $conversation): bool
    {
        $canRestore = $user->id === $conversation->user_id_1 || $user->id === $conversation->user_id_2;
        
        if (!$canRestore) {
            Log::warning('Unauthorized conversation restore attempt', [
                'user_id' => $user->id,
                'conversation_id' => $conversation->id
            ]);
        }
        
        return $canRestore;
    }
}
