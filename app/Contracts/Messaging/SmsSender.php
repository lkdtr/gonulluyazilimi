<?php

namespace App\Contracts\Messaging;

/**
 * Sends a text message to a phone number (NetGSM, or the log locally).
 */
interface SmsSender
{
    public function send(string $phone, string $text): void;
}
