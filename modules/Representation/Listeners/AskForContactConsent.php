<?php

namespace Modules\Representation\Listeners;

use App\Events\ProfileUpdated;
use App\Models\Cities;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Modules\Representation\Models\LegalRepresentation;
use Modules\Representation\Models\LegalRepresentationVolunteer;

/**
 * After a member updates their own profile, sends them to the contact consent page of the
 * representation in their city unless they have already answered it.
 */
class AskForContactConsent
{
    public function handle(ProfileUpdated $event): void
    {
        $user = $event->user;

        // Consent is given by the member; an admin editing someone's profile is not asked.
        if (! $user->is(Auth::user())) {
            return;
        }

        $representation = LegalRepresentation::where('city', Cities::find($user->city_id)?->city_name)->where('status', true)->first();
        if ($representation && ! LegalRepresentationVolunteer::where('legal_representation_id', $representation->id)->where('user_id', $user->id)->exists()) {
            $event->redirect = Redirect::route('representations.consent', $representation);
        }
    }
}
