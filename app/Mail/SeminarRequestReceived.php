<?php

namespace App\Mail;

use App\Models\SeminarRequests;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SeminarRequestReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SeminarRequests $seminarRequest)
    {
    }

    public function build(): self
    {
        return $this->subject('Seminer talebiniz alındı')
            ->view('emails.seminar_request_received');
    }
}
