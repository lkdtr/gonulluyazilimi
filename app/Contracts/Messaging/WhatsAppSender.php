<?php

namespace App\Contracts\Messaging;

/**
 * Sends a WhatsApp message to a phone number (the WhatsApp bridge, or the
 * log locally). available() is false when no bridge is configured.
 */
interface WhatsAppSender
{
    public function available(): bool;

    public function send(string $phone, string $text): void;
}
