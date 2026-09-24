<?php

namespace Modules\Volunteer\Listeners;

use App\Models\AffiliationType;
use Illuminate\Auth\Events\Registered;

/**
 * Registering on the volunteer site makes the person a volunteer.
 */
class MarkAsVolunteer
{
    public function handle(Registered $event): void
    {
        $event->user->contact?->affiliate(AffiliationType::VOLUNTEER);
    }
}
