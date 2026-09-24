<?php

namespace Modules\MailForwarding\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\MailForwarding\Support\ForwardingPolicy;

class ForwardingSettingsController extends Controller
{
    public function edit(ForwardingPolicy $policy): View
    {
        return view('mail-forwarding::admin.settings', [
            'types' => $policy->affiliationTypes(),
            'domains' => $policy->domains(),
            'label' => $policy->label(),
        ]);
    }

    public function update(Request $request, ForwardingPolicy $policy, Organization $organization): RedirectResponse
    {
        $types = array_keys($policy->affiliationTypes());
        $data = $request->validate([
            'domains' => ['array'],
            'domains.*' => ['nullable', 'string', 'max:100', 'regex:/^[a-z0-9-]+(\.[a-z0-9-]+)+$/i'],
            'label' => ['required', 'string', 'max:60'],
        ], [], ['domains.*' => 'Alan adı', 'label' => 'Adresin adı']);

        $domains = collect($data['domains'] ?? [])->only($types)->map(fn ($domain) => strtolower(trim((string) $domain)))->filter()->all();

        $organization->save([
            'mail_forwarding_domains' => json_encode($domains),
            'mail_forwarding_label' => $data['label'],
        ]);
        $this->set_log('change', 'E-posta yönlendirme ayarları güncellendi');

        return back()->with('success-status', 'E-posta yönlendirme ayarları kaydedildi.');
    }
}
