<?php

namespace App\Mail;

use App\Models\EmailChangeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailChangeRequestProcessed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public EmailChangeRequest $emailChangeRequest)
    {
    }

    public function build(): self
    {
        $subject = $this->emailChangeRequest->status === 'approved'
            ? 'E-posta değişikliği talebiniz onaylandı'
            : 'E-posta değişikliği talebiniz sonuçlandı';

        return $this->subject($subject)->view('emails.email_change_request_processed');
    }
}
