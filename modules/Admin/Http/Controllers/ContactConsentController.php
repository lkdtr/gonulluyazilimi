<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Support\Consents;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactConsentController extends Controller
{
    public function update(Request $request, Contact $contact, Consents $consents): RedirectResponse
    {
        $changed = $consents->set($contact, collect(Consents::CHANNELS)->map(fn ($label, $channel) => $request->boolean("consents.{$channel}"))->all(), 'admin');

        if ($changed) {
            $this->set_log('change', "İletişim izinleri güncellendi: {$contact->display_name}");
        }

        return back()->with('success-status', $changed ? 'İletişim izinleri kaydedildi.' : 'İzinlerde değişiklik yok.');
    }
}
