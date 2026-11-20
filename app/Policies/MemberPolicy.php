<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;

class MemberPolicy
{
    /**
     * Determine whether the user can view sensitive data (e.g. decrypted phone number) of the member (FR-011).
     */
    public function viewSensitive(User $user, Member $member): bool
    {
        return $user->id === $member->user_id || $user->isAdmin();
    }
}
