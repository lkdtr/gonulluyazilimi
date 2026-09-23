<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Http\RedirectResponse;

/**
 * Fired after a user's profile is saved. A listener may set $redirect to send
 * the user to a follow-up page instead of back to the profile form.
 */
class ProfileUpdated
{
    public ?RedirectResponse $redirect = null;

    public function __construct(public User $user)
    {
    }
}
