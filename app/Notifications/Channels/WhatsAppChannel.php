<?php

namespace App\Notifications\Channels;

use App\Contracts\Messaging\WhatsAppSender;
use Illuminate\Notifications\Notification;

/**
 * Notification channel over the configured WhatsAppSender; silently skipped
 * when no bridge is available.
 */
class WhatsAppChannel
{
    public function __construct(private WhatsAppSender $sender)
    {
    }

    public function send(object $notifiable, Notification $notification): void
    {
        if ($this->sender->available() && ($phone = $notifiable->routeNotificationFor('whatsapp', $notification))) {
            $this->sender->send($phone, $notification->toWhatsApp($notifiable));
        }
    }
}
