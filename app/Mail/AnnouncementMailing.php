<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AnnouncementMailing extends Mailable
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

        [$address, $name] = app(\App\Support\Organization::class)->sender();

        return $this->from($address, $name)
                    ->subject($this->data->subject)
                    ->view('emails.mailing_layout')
                    ->with(['data' => $this->data]);
    }
}
