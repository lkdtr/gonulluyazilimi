<?php

namespace Modules\IdCard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Modules\IdCard\Models\IdCard;
use Modules\IdCard\Models\IdCardTemplate;
use Modules\IdCard\Support\CardIssuer;
use Modules\IdCard\Support\CardView;
use Symfony\Component\HttpFoundation\Response;

class CardController extends Controller
{
    /**
     * The signed-in person's cards ("Kartlarım").
     */
    public function index(CardIssuer $issuer, CardView $view): View
    {
        $contact = Auth::user()->syncContact();
        $photo = $contact->approvedPhoto;

        return view('id-card::index', [
            'contact' => $contact,
            'photo' => $photo,
            'cards' => $issuer->cardsFor($contact)->map(fn (IdCard $card) => [
                'card' => $card,
                'fields' => $view->fields($card->template, $contact),
                'qr' => $view->qrSvg($card),
                // The card is shown once the photo it requires is approved.
                'ready' => ! $card->template->requires_photo || $photo !== null,
            ]),
        ]);
    }

    /**
     * Give the card a new QR code; the old one stops verifying.
     */
    public function renew(IdCard $card): RedirectResponse
    {
        abort_unless($card->contact_id === Auth::user()->contact_id, 403);

        $card->renewVerifyToken();
        $this->set_log('change', "Kimlik kartı QR kodu yenilendi: {$card->number}");

        return redirect()->route('id-cards')->with('success-status', 'Kartınızın QR kodu yenilendi; eski QR kod artık doğrulanmaz.');
    }

    /**
     * Public organization logo of a card template.
     */
    public function logo(IdCardTemplate $template): Response
    {
        abort_unless($template->logo_path && Storage::disk('local')->exists($template->logo_path), 404);

        return Storage::disk('local')->response($template->logo_path, null, [
            'Cache-Control' => 'public, max-age=86400',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
