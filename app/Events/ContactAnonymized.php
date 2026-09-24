<?php

namespace App\Events;

use App\Models\Contact;

/**
 * Fired inside the transaction that anonymizes a contact (KVKK deletion).
 * Modules remove or anonymize the personal data they hold for the contact
 * and its account ($userId, null when it had none).
 */
class ContactAnonymized
{
    public function __construct(public Contact $contact, public ?int $userId)
    {
    }
}
