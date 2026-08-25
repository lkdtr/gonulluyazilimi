<?php

namespace App\Mail;

use App\Models\EmailChangeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailChangeRequestSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public EmailChangeRequest $emailChangeRequest)
    {
    }

    public function build(): self
    {
        return $this->subject('Gönüllü sistemi e-posta değişikliği talebi')
            ->view('emails.email_change_request_submitted');
    }
}
