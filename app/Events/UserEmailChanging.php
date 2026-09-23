<?php

namespace App\Events;

use App\Models\User;

/**
 * Fired before a user's account e-mail is changed. Listeners that cannot
 * follow the change throw App\Exceptions\ActionBlocked to cancel it.
 * With $dryRun only the checks run; nothing may be changed.
 */
class UserEmailChanging
{
    public function __construct(public User $user, public string $newEmail, public bool $dryRun = false)
    {
    }
}
