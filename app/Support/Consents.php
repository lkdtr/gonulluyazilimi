<?php

namespace App\Support;

use App\Models\ConsentEvent;
use App\Models\Contact;
use Illuminate\Support\Facades\Auth;

/**
 * Communication consents of a contact. The latest event per channel is the
 * consent in force; set() records an event only when a channel changes.
 * Messaging must ask allows() before sending on a channel.
 */
class Consents
{
    public const CHANNELS = [
        'email' => 'E-posta ile duyuru ve bilgilendirme',
        'sms' => 'SMS ile duyuru ve bilgilendirme',
        'whatsapp' => 'WhatsApp ile duyuru ve bilgilendirme',
        'phone' => 'Telefonla arama',
    ];

    public const SOURCES = [
        'profile' => 'Profil sayfası',
        'register' => 'Kayıt',
        'admin' => 'Yönetici',
        'import' => 'İçe aktarma',
        'deletion' => 'Veri silme',
    ];

    /**
     * Channel => granted (true/false), or null when never asked.
     *
     * @return array<string, bool|null>
     */
    public function current(Contact $contact): array
    {
        $latest = ConsentEvent::where('contact_id', $contact->id)
            ->whereIn('id', ConsentEvent::selectRaw('max(id)')->where('contact_id', $contact->id)->groupBy('channel'))
            ->pluck('granted', 'channel');

        return collect(self::CHANNELS)->map(fn ($label, $channel) => $latest->has($channel) ? (bool) $latest[$channel] : null)->all();
    }

    public function allows(Contact $contact, string $channel): bool
    {
        return $this->current($contact)[$channel] ?? false;
    }

    /**
     * @param  array<string, bool>  $channels  channel => granted; channels left out are unchanged
     * @return int number of changes recorded
     */
    public function set(Contact $contact, array $channels, string $source): int
    {
        $current = $this->current($contact);
        $request = request();
        $recorded = 0;

        foreach ($channels as $channel => $granted) {
            if (! array_key_exists($channel, self::CHANNELS) || $current[$channel] === (bool) $granted) {
                continue;
            }

            ConsentEvent::create([
                'contact_id' => $contact->id,
                'channel' => $channel,
                'granted' => (bool) $granted,
                'source' => $source,
                'user_id' => Auth::id(),
                'ip' => $request->ip(),
                'user_agent' => mb_substr((string) $request->userAgent(), 0, 255) ?: null,
            ]);
            $recorded++;
        }

        return $recorded;
    }
}
