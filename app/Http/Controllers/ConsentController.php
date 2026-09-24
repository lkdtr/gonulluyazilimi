<?php

namespace App\Http\Controllers;

use App\Support\Consents;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * The signed-in person's communication consents, on the profile page.
 */
class ConsentController extends Controller
{
    public function update(Request $request, Consents $consents): RedirectResponse
    {
        $contact = Auth::user()->syncContact();

        $changed = $consents->set($contact, collect(Consents::CHANNELS)->map(fn ($label, $channel) => $request->boolean("consents.{$channel}"))->all(), 'profile');

        return redirect(route('my-infos').'#consents')->with('consent-status', $changed ? 'İletişim izinleriniz güncellendi.' : 'İletişim izinlerinizde değişiklik yok.');
    }
}
