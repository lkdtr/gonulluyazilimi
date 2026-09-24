<?php

namespace App\Support\Messaging;

use App\Contracts\Messaging\SmsSender;
use BahriCanli\Netgsm\ShortMessage;
use NotificationChannels\Netgsm\Netgsm;

class NetgsmSmsSender implements SmsSender
{
    public function send(string $phone, string $text): void
    {
        // NetGSM's default encoding has no Turkish letters.
        $text = str_replace(['ı', 'ü', 'ö', 'ç', 'ş', 'ğ', 'İ', 'Ü', 'Ö', 'Ç', 'Ş', 'Ğ'], ['i', 'u', 'o', 'c', 's', 'g', 'I', 'U', 'O', 'C', 'S', 'G'], $text);

        Netgsm::sendShortMessage(new ShortMessage($phone, $text));
    }
}
