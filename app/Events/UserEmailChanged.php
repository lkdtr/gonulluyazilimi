<?php

namespace App\Events;

use App\Models\User;

/**
 * Fired inside the database transaction that changes a user's account e-mail.
 */
class UserEmailChanged
{
    public function __construct(public User $user, public string $oldEmail)
    {
    }
}
