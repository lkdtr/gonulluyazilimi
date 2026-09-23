<?php

namespace Modules\Seminar\Mail;

use Modules\Seminar\Models\SeminarOffers;
use Illuminate\Mail\Mailable;

class SeminarOfferReceived extends Mailable
{
    public function __construct(public SeminarOffers $seminarOffer) {}
    public function build(): self { return $this->subject('Seminer verme başvurunuz alındı')->view('seminar::emails.offer_received'); }
}
