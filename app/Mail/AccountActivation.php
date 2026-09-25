<?php

namespace App\Mail;

use App\Support\AccountActivation as Activation;
use App\Support\Organization;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountActivation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public string $name, public string $link)
    {
    }

    public function build(): self
    {
        return $this->subject(app(Organization::class)->name().' portalı hesabınızı etkinleştirin')
            ->view('emails.account-activation', ['hours' => Activation::LINK_HOURS]);
    }
}
