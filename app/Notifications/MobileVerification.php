<?php

namespace App\Notifications;

use App\Contracts\Messaging\WhatsAppSender;
use App\Notifications\Channels\SmsChannel;
use App\Notifications\Channels\WhatsAppChannel;
use App\Support\Organization;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * Phone verification code, sent by SMS and, when a WhatsApp bridge is
 * available, by WhatsApp in parallel.
 */
class MobileVerification extends Notification
{
    public function via($notifiable): array
    {
        return app(WhatsAppSender::class)->available() ? [SmsChannel::class, WhatsAppChannel::class] : [SmsChannel::class];
    }

    public function toSms($notifiable): string
    {
        Log::info('Validate Phone Sent (SMS) '.$notifiable->phone_number);

        return $this->text($notifiable);
    }

    public function toWhatsApp($notifiable): string
    {
        Log::info('Validate Phone Sent (WhatsApp) '.$notifiable->phone_number);

        return $this->text($notifiable);
    }

    private function text($notifiable): string
    {
        return $notifiable->verification_code.' kodu ile telefon numaranızı doğrulayabilirsiniz. '.app(Organization::class)->name();
    }
}
