<?php

namespace App\Support;

use App\Mail\AccountActivation as ActivationMail;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

/**
 * First sign-in of a contact without an account (imported members, people a
 * manager added): a signed, expiring link sent to the contact's email lets
 * them set a password. The account is linked to the existing contact, so its
 * affiliations and membership stay.
 */
class AccountActivation
{
    public const LINK_HOURS = 48;

    public function eligible(Contact $contact): bool
    {
        return ! $contact->trashed()
            && ! $contact->isOrganization()
            && filter_var($contact->email, FILTER_VALIDATE_EMAIL)
            && ! $contact->user()->exists()
            && ! User::where('email', strtolower($contact->email))->exists();
    }

    /**
     * The contact that may activate an account with this email, if exactly one.
     */
    public function findByEmail(string $email): ?Contact
    {
        $contacts = Contact::where('email', strtolower(trim($email)))->whereDoesntHave('user')->limit(2)->get();

        return $contacts->count() === 1 && $this->eligible($contacts->first()) ? $contacts->first() : null;
    }

    /**
     * Signed link; it stops working when the contact's email changes.
     */
    public function link(Contact $contact): string
    {
        return URL::temporarySignedRoute('account.activate', now()->addHours(self::LINK_HOURS), [
            'contact' => $contact->id,
            'hash' => $this->hash($contact),
        ]);
    }

    public function hash(Contact $contact): string
    {
        return substr(sha1(strtolower((string) $contact->email).'|'.$contact->id), 0, 16);
    }

    public function send(Contact $contact): bool
    {
        if (! $this->eligible($contact)) {
            return false;
        }

        Mail::to($contact->email)->send(new ActivationMail($contact->display_name, $this->link($contact)));

        return true;
    }
}
