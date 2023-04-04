<?php

namespace App\Notifications;

use TarfinLabs\Netgsm\NetGsmChannel;
use TarfinLabs\Netgsm\NetGsmSmsMessage;
use Illuminate\Notifications\Notification;

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
        return [NetGsmChannel::class];
    }

    public function toNetgsm($notifiable)
    {
        Log::info("Validate Phone Sent ".$notifiable->phone_number);

        return (new NetGsmSmsMessage("Merhaba, Telefon dogrulama kodunuz {$notifiable->verification_code}"))->setRecipients($notifiable->phone_number);
    }
}
