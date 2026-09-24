<?php

namespace Modules\IdCard\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\IdCard\Models\IdCard;
use Modules\IdCard\Models\IdCardTemplate;

class IssuedCardController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $cards = IdCard::with(['template.affiliationType', 'contact', 'affiliation'])
            ->when($request->integer('template'), fn ($query, $template) => $query->where('id_card_template_id', $template))
            ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query
                ->where('number', 'like', "%{$search}%")
                ->orWhereHas('contact', fn ($query) => $query
                    ->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%"))))
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('id-card::admin.cards.index', [
            'cards' => $cards,
            'templates' => IdCardTemplate::with('affiliationType')->get(),
            'search' => $search,
        ]);
    }

    public function revoke(Request $request, IdCard $card): RedirectResponse
    {
        $data = $request->validate(['reason' => ['nullable', 'string', 'max:255']], [], ['reason' => 'Gerekçe']);

        $card->forceFill(['revoked_at' => now(), 'revoked_reason' => $data['reason'] ?? null])->save();
        $this->set_log('change', "Kimlik kartı iptal edildi: {$card->number}");

        return back()->with('success-status', "{$card->number} numaralı kart iptal edildi.");
    }

    public function restore(IdCard $card): RedirectResponse
    {
        $card->forceFill(['revoked_at' => null, 'revoked_reason' => null])->save();
        $this->set_log('change', "Kimlik kartı iptali kaldırıldı: {$card->number}");

        return back()->with('success-status', "{$card->number} numaralı kartın iptali kaldırıldı.");
    }

    public function renew(IdCard $card): RedirectResponse
    {
        $card->renewVerifyToken();
        $this->set_log('change', "Kimlik kartı QR kodu yenilendi: {$card->number}");

        return back()->with('success-status', "{$card->number} numaralı kartın QR kodu yenilendi.");
    }
}
