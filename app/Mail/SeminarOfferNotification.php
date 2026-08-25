<?php

namespace App\Mail;

use App\Models\SeminarOffers;
use Illuminate\Mail\Mailable;

class SeminarOfferNotification extends Mailable
{
    public function __construct(public SeminarOffers $seminarOffer) {}
    public function build(): self { return $this->subject('Yeni seminer verme başvurusu')->view('emails.seminar_offer_notification'); }
}
