<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SupportRequest;

class SupportRequestPolicy
{
    public function view(User $user, SupportRequest $supportRequest): bool
    {
        return $user->id === $supportRequest->user_id || $user->isAdmin();
    }

    public function reply(User $user, SupportRequest $supportRequest): bool
    {
        return $user->id === $supportRequest->user_id || $user->isAdmin();
    }
}