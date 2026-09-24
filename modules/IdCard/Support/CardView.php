<?php

namespace Modules\IdCard\Support;

use App\Models\Contact;
use App\Modules\ContactFields;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;
use Modules\IdCard\Models\IdCard;
use Modules\IdCard\Models\IdCardTemplate;

/**
 * What a card shows: field values, the masked name of the verification page
 * and the QR code.
 */
class CardView
{
    public function __construct(private ContactFields $fields)
    {
    }

    /**
     * Label => value of the template's fields that have a value for the contact.
     *
     * @return array<string, string>
     */
    public function fields(IdCardTemplate $template, Contact $contact): array
    {
        $values = [];
        foreach ($template->fields ?? [] as $key) {
            $value = $this->fields->value($key, $contact);
            if ($value !== null) {
                $values[$this->fields->label($key)] = $value;
            }
        }

        return $values;
    }

    /**
     * Sample values for the template preview.
     *
     * @return array<string, string>
     */
    public function sampleFields(IdCardTemplate $template): array
    {
        $values = [];
        foreach ($template->fields ?? [] as $key) {
            if ($label = $this->fields->label($key)) {
                $values[$label] = match ($key) {
                    'member_number' => '1234',
                    'forwarding_email' => 'ad.soyad@example.org',
                    'email' => 'ad.soyad@example.org',
                    'phone' => '+90 555 000 00 00',
                    'city' => 'İstanbul',
                    default => '…',
                };
            }
        }

        return $values;
    }

    /**
     * "Ahmet Y****": first names in full, the surname reduced to its initial,
     * so the public verification page does not reveal who holds a card.
     */
    public static function maskedName(Contact $contact): string
    {
        $initial = mb_substr(trim((string) $contact->last_name), 0, 1);

        return trim(trim((string) $contact->first_name).($initial !== '' ? ' '.mb_strtoupper($initial, 'UTF-8').'****' : ''));
    }

    /**
     * Verification address on the site's own domain (APP_URL), whichever
     * domain the card page was opened on: the QR code outlives the visit.
     */
    public static function verifyUrl(IdCard $card): string
    {
        return rtrim((string) config('app.url'), '/').route('id-card.verify', $card->verify_token, false);
    }

    public function qrSvg(IdCard $card, int $size = 200): string
    {
        $writer = new Writer(new ImageRenderer(new RendererStyle($size, 1), new SvgImageBackEnd()));
        $svg = $writer->writeString(self::verifyUrl($card));

        // Inline markup: drop the XML declaration.
        return preg_replace('/^<\?xml[^>]*>\s*/', '', $svg);
    }
}
