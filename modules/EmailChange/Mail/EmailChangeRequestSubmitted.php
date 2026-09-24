<?php

namespace Modules\EmailChange\Mail;

use Modules\EmailChange\Models\EmailChangeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailChangeRequestSubmitted extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public EmailChangeRequest $emailChangeRequest)
    {
    }

    public function build(): self
    {
        return $this->subject('Gönüllü sistemi e-posta değişikliği talebi')
            ->view('email-change::emails.submitted');
    }
}
