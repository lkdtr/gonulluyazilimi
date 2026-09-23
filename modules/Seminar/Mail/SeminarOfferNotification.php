<?php

namespace Modules\Seminar\Mail;

use Modules\Seminar\Models\SeminarOffers;
use Illuminate\Mail\Mailable;

class SeminarOfferNotification extends Mailable
{
    public function __construct(public SeminarOffers $seminarOffer) {}
    public function build(): self { return $this->subject('Yeni seminer verme başvurusu')->view('seminar::emails.offer_notification'); }
}
