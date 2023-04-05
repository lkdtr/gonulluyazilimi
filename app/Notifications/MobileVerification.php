<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use NotificationChannels\Netgsm\NetgsmChannel;
use NotificationChannels\Netgsm\NetgsmMessage;
use BahriCanli\Netgsm\ShortMessage;

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
    public function via($notifiable)
    {
        return [NetgsmChannel::class];
    }

    public function toNetgsm($notifiable)
    {
        Log::info("Validate Phone Sent ".$notifiable->phone_number);

        $message = $notifiable->verification_code. " kodu ile telefon numaranızı doğrulayabilirsiniz. Linux Kullanıcıları Derneği";
        $message = str_replace(["ı", "ü", "ö", "ç", "ş", "ğ", "İ", "Ü", "Ö"],["i", "u", "o", "c", "s", "g", "I", "U", "O"], $message);
        return new ShortMessage($notifiable->phone_number, $message);
    }
}
