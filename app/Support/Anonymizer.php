<?php

namespace App\Support;

use App\Events\ContactAnonymized;
use App\Models\Contact;
use App\Models\ContactPhoto;
use App\Models\PhoneVerification;
use App\Models\ProcessLogs;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Removes the personal data of a contact and its account (KVKK deletion).
 * Rows stay so references (agreement acceptances, affiliation history, audit
 * entries) remain valid, but they no longer identify the person. Modules
 * clean their own data on ContactAnonymized.
 */
class Anonymizer
{
    public const NAME = 'Silinmiş';

    public const SURNAME = 'Kişi';

    public function __construct(private Consents $consents)
    {
    }

    public function anonymize(Contact $contact, string $reference): void
    {
        DB::transaction(function () use ($contact, $reference) {
            $user = $contact->user;
            $phone = $user?->phone_number ?? $contact->phone;
            $emails = array_filter([$user?->email, $contact->email]);
            $photoIds = $contact->photos()->pluck('id')->all();
            $customValueIds = $contact->customFieldValues()->pluck('id')->all();

            // The audit entries of the anonymization itself must not store the
            // values being removed.
            Audit::withoutRecording(function () use ($contact, $user, $phone, $emails, $photoIds, $customValueIds) {
                // Withdraw every consent; the history stays as proof.
                $this->consents->set($contact, array_fill_keys(array_keys(Consents::CHANNELS), false), 'deletion');

                $contact->photos()->get()->each(fn (ContactPhoto $photo) => $photo->delete());
                $contact->customFieldValues()->delete();
                $contact->tags()->detach();
                $contact->affiliations()->active()->update(['ended_at' => today()]);

                if ($user) {
                    $user->roles()->detach();
                    $user->forceFill([
                        'name' => self::NAME,
                        'surname' => self::SURNAME,
                        'email' => 'silinmis-'.$user->id.'@invalid.invalid',
                        'phone_number' => null,
                        'national_id' => null,
                        'birthday' => null,
                        'city_id' => 0,
                        'lkd_user_id' => null,
                        'role' => 3,
                        'status' => 0,
                        'password' => bcrypt(Str::random(40)),
                        'remember_token' => null,
                    ])->saveQuietly();
                }

                if ($phone) {
                    PhoneVerification::where('value', preg_replace('/\D/', '', $phone))->delete();
                }

                $contact->forceFill([
                    'first_name' => self::NAME,
                    'last_name' => self::SURNAME,
                    'organization_name' => $contact->isOrganization() ? self::NAME.' kurum' : null,
                    'identity_number' => null,
                    'email' => null,
                    'phone' => null,
                    'birthday' => null,
                    'city_id' => null,
                ])->saveQuietly();

                event(new ContactAnonymized($contact, $user?->id));

                $this->scrubAuditHistory($contact, $user, $photoIds, $emails, $customValueIds);
            });

            $contact->delete();

            Audit::record('other', $contact, [], "Kişisel veriler silindi ({$reference})");
        });
    }

    /**
     * Earlier audit entries of the contact and its account carry old values;
     * keep that something happened, not what the values were.
     */
    private function scrubAuditHistory(Contact $contact, ?User $user, array $photoIds, array $emails, array $customValueIds): void
    {
        $subjects = [
            [Contact::class, [$contact->id]],
            [ContactPhoto::class, $photoIds],
            [\App\Models\ContactAffiliation::class, $contact->affiliations()->pluck('id')->all()],
            [\App\Models\CustomFieldValue::class, $customValueIds],
        ];
        if ($user) {
            $subjects[] = [User::class, [$user->id]];
        }

        foreach ($subjects as [$type, $ids]) {
            if ($ids === []) {
                continue;
            }
            ProcessLogs::where('subject_type', $type)->whereIn('subject_id', $ids)->each(function (ProcessLogs $log) {
                $log->changes = $log->changes ? array_map(fn () => ['•••', '•••'], $log->changes) : null;
                $log->process = preg_replace('/:.*$/u', ': [silindi]', (string) $log->process);
                $log->save();
            });
        }

        // Free-text entries written by older code name the account's address.
        foreach ($emails as $email) {
            ProcessLogs::where('process', 'like', '%'.$email.'%')->each(function (ProcessLogs $log) use ($email) {
                $log->process = str_replace($email, '[silindi]', (string) $log->process);
                $log->save();
            });
        }
    }
}
