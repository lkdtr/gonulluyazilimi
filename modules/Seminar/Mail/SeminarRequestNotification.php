<?php

namespace Modules\Seminar\Mail;

use Modules\Seminar\Models\SeminarRequests;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SeminarRequestNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public SeminarRequests $seminarRequest)
    {
    }

    public function build(): self
    {
        return $this->subject('Yeni seminer talebi')
            ->view('seminar::emails.request_notification');
    }
}
