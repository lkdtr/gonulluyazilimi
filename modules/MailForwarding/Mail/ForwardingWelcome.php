<?php

namespace Modules\MailForwarding\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForwardingWelcome extends Mailable
{
    use Queueable, SerializesModels;

    private $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(ucfirst(config('mail-forwarding.domain'))." e-postanız aktif edildi")
                    ->view('mail-forwarding::emails.welcome')
                    ->with(['data' => $this->data]);
    }
}
