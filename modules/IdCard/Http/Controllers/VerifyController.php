<?php

namespace Modules\IdCard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Modules\IdCard\Models\IdCard;
use Modules\IdCard\Support\CardView;

/**
 * Public page the card's QR code opens. It confirms whether the card is valid
 * and shows only the masked name, the card type and the card number.
 */
class VerifyController extends Controller
{
    public function show(string $token): Response
    {
        $card = IdCard::with(['template.affiliationType', 'contact', 'affiliation'])->where('verify_token', $token)->first();

        return response()->view('id-card::verify', [
            'card' => $card,
            'status' => $card?->status(),
            'maskedName' => $card ? CardView::maskedName($card->contact) : null,
        ], $card ? 200 : 404)->header('X-Robots-Tag', 'noindex, nofollow');
    }
}
