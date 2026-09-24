<?php

namespace App\Support\Messaging;

use App\Contracts\Messaging\WhatsAppSender;
use NotificationChannels\WhatsAppBridge\WhatsApp;

class WhatsAppBridgeSender implements WhatsAppSender
{
    public function available(): bool
    {
        return (bool) config('whatsapp-bridge.bridge_url');
    }

    public function send(string $phone, string $text): void
    {
        app(WhatsApp::class)->sendMessage($phone, $text);
    }
}
