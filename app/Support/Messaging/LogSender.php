<?php

namespace App\Support\Messaging;

use App\Contracts\Messaging\SmsSender;
use App\Contracts\Messaging\WhatsAppSender;
use Illuminate\Support\Facades\Log;

/**
 * Writes messages to the log instead of sending them (local, testing).
 */
class LogSender implements SmsSender, WhatsAppSender
{
    /** @var array<int, array{channel: string, phone: string, text: string}> */
    public array $sent = [];

    public function __construct(private string $channel = 'sms')
    {
    }

    public function available(): bool
    {
        return true;
    }

    public function send(string $phone, string $text): void
    {
        $this->sent[] = ['channel' => $this->channel, 'phone' => $phone, 'text' => $text];
        Log::info("[{$this->channel}] {$phone}: {$text}");
    }
}
