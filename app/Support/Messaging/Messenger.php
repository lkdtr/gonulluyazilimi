<?php

namespace App\Support\Messaging;

use App\Contracts\Messaging\SmsSender;
use App\Contracts\Messaging\WhatsAppSender;
use App\Models\Contact;
use App\Support\Consents;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * One place to reach a contact on email, SMS or WhatsApp. Informational
 * messages go only through channels the contact consented to; transactional
 * ones (verification codes, application results) use the senders directly.
 */
class Messenger
{
    public function __construct(private SmsSender $sms, private WhatsAppSender $whatsApp, private Consents $consents)
    {
    }

    public function sms(): SmsSender
    {
        return $this->sms;
    }

    public function whatsApp(): WhatsAppSender
    {
        return $this->whatsApp;
    }

    /**
     * Send an informational message on the given channels the contact
     * consented to.
     *
     * @param  string[]  $channels  email, sms, whatsapp
     * @return string[] channels it was sent on
     */
    public function inform(Contact $contact, string $subject, string $text, array $channels = ['email']): array
    {
        $sent = [];
        foreach ($channels as $channel) {
            if (! $this->consents->allows($contact, $channel)) {
                continue;
            }

            $address = $channel === 'email' ? $contact->email : $contact->phone;
            if (! $address || ($channel === 'whatsapp' && ! $this->whatsApp->available())) {
                continue;
            }

            try {
                match ($channel) {
                    'email' => Mail::raw($text, fn ($message) => $message->to($address)->subject($subject)),
                    'sms' => $this->sms->send($address, $text),
                    'whatsapp' => $this->whatsApp->send($address, $text),
                };
                $sent[] = $channel;
            } catch (Throwable $e) {
                report($e);
            }
        }

        return $sent;
    }
}
