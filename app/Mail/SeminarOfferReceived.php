<?php

namespace App\Mail;

use App\Models\SeminarOffers;
use Illuminate\Mail\Mailable;

class SeminarOfferReceived extends Mailable
{
    public function __construct(public SeminarOffers $seminarOffer) {}
    public function build(): self { return $this->subject('Seminer verme başvurunuz alındı')->view('emails.seminar_offer_received'); }
}
