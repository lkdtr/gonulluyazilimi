<?php

namespace Modules\EmailChange\Support;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

trait SendsNotifications
{
    /**
     * The request is already saved when these notifications go out; a mail
     * delivery failure must not turn a completed action into an error page.
     */
    protected function sendMail(string $to, Mailable $mailable): void
    {
        try {
            Mail::to($to)->send($mailable);
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
