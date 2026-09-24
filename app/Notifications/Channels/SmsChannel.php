<?php

namespace App\Notifications\Channels;

use App\Contracts\Messaging\SmsSender;
use Illuminate\Notifications\Notification;

/**
 * Notification channel over the configured SmsSender. The notification
 * provides toSms($notifiable): string; the notifiable routeNotificationForSms().
 */
class SmsChannel
{
    public function __construct(private SmsSender $sender)
    {
    }

    public function send(object $notifiable, Notification $notification): void
    {
        if ($phone = $notifiable->routeNotificationFor('sms', $notification)) {
            $this->sender->send($phone, $notification->toSms($notifiable));
        }
    }
}
