<?php

namespace Modules\Seminar\Mail;

use Modules\Seminar\Models\SeminarOffers;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SeminarOfferNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public SeminarOffers $seminarOffer) {}
    public function build(): self { return $this->subject('Yeni seminer verme başvurusu')->view('seminar::emails.offer_notification'); }
}
