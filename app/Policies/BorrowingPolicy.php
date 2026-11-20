<?php

namespace App\Policies;

use App\Models\Borrowing;
use App\Models\User;

class BorrowingPolicy
{
    /**
     * Determine whether the user can view the borrowing details (SEC-10 Anti-IDOR).
     */
    public function view(User $user, Borrowing $borrowing): bool
    {
        return $user->id === $borrowing->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can return the borrowed book (BR-04).
     */
    public function return(User $user, Borrowing $borrowing): bool
    {
        return $user->id === $borrowing->user_id || $user->isAdmin();
    }
}
