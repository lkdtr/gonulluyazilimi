<?php

namespace Modules\EmailChange\Mail;

use Modules\EmailChange\Models\EmailChangeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailChangeRequestProcessed extends Mailable implements ShouldQueue
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

        return $this->subject($subject)->view('email-change::emails.processed');
    }
}
