<?php

namespace App\Notifications;

use Illuminate\Notifications\Notifiable;

class PhoneVerificationRecipient
{
    use Notifiable;

    public function __construct(
        public readonly string $phone_number,
        public readonly string $verification_code,
    ) {
    }

    public function getKey(): string
    {
        return $this->phone_number;
    }
}
