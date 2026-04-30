<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use NotificationChannels\Netgsm\NetgsmChannel;
use NotificationChannels\Netgsm\NetgsmMessage;
use BahriCanli\Netgsm\ShortMessage;

use NotificationChannels\WhatsAppBridge\WhatsAppChannel;
use NotificationChannels\WhatsAppBridge\WhatsAppMessage;

use Illuminate\Support\Facades\Log;

class MobileVerification extends Notification
{

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    /**
     * SMS (NetGSM) ve WhatsApp paralel olarak gönderilir.
     * WHATSAPP_BRIDGE_URL tanımlı değilse sadece SMS gönderilir.
     */
    public function via($notifiable)
    {
        $channels = [NetgsmChannel::class];

        if (config('whatsapp-bridge.bridge_url')) {
            $channels[] = WhatsAppChannel::class;
        }

        return $channels;
    }

    public function toNetgsm($notifiable)
    {
        Log::info("Validate Phone Sent (SMS) ".$notifiable->phone_number);

        $message = $notifiable->verification_code. " kodu ile telefon numaranızı doğrulayabilirsiniz. Linux Kullanıcıları Derneği";
        $message = str_replace(["ı", "ü", "ö", "ç", "ş", "ğ", "İ", "Ü", "Ö"],["i", "u", "o", "c", "s", "g", "I", "U", "O"], $message);
        return new ShortMessage($notifiable->phone_number, $message);
    }

    public function toWhatsApp($notifiable)
    {
        Log::info("Validate Phone Sent (WhatsApp) ".$notifiable->phone_number);

        $message = $notifiable->verification_code . " kodu ile telefon numaranızı doğrulayabilirsiniz. Linux Kullanıcıları Derneği";

        return WhatsAppMessage::create($message)->to($notifiable->phone_number);
    }
}
