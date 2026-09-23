<?php

namespace Modules\Representation\Listeners;

use App\Events\ProfileUpdated;
use App\Models\Cities;
use Illuminate\Support\Facades\Redirect;
use Modules\Representation\Models\LegalRepresentation;
use Modules\Representation\Models\LegalRepresentationVolunteer;

/**
 * After a profile update, sends the user to the contact consent page of the
 * representation in their city unless they have already answered it.
 */
class AskForContactConsent
{
    public function handle(ProfileUpdated $event): void
    {
        $user = $event->user;

        $representation = LegalRepresentation::where('city', Cities::find($user->city_id)?->city_name)->where('status', true)->first();
        if ($representation && ! LegalRepresentationVolunteer::where('legal_representation_id', $representation->id)->where('user_id', $user->id)->exists()) {
            $event->redirect = Redirect::route('representations.consent', $representation);
        }
    }
}
