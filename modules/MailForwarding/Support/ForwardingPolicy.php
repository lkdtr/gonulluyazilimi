<?php

namespace Modules\MailForwarding\Support;

use App\Models\AffiliationType;
use App\Models\ContactAffiliation;
use App\Models\User;
use App\Support\Organization;

/**
 * Who gets an association address and on which domain: each affiliation
 * type can have its own domain (e.g. volunteers @penguen.org.tr, members
 * @linux.org.tr); a person without such an affiliation gets none.
 * Set in the admin panel (mail_forwarding_domains setting).
 */
class ForwardingPolicy
{
    public function __construct(private Organization $organization)
    {
    }

    /**
     * Affiliation type key => domain.
     *
     * @return array<string, string>
     */
    public function domains(): array
    {
        $stored = json_decode((string) $this->organization->get('mail_forwarding_domains'), true);
        if (is_array($stored)) {
            return array_filter($stored);
        }

        // Until configured: the domain of MAIL_FORWARDING_DOMAIN for volunteers, as before.
        return config('mail-forwarding.domain') ? ['volunteer' => config('mail-forwarding.domain')] : [];
    }

    /**
     * Domains the user may hold an address on, in affiliation order, each
     * with the affiliation names that give it: [domain => 'Gönüllü', ...].
     * Someone both volunteer and member may get two addresses.
     *
     * @return array<string, string>
     */
    public function domainsFor(?User $user): array
    {
        $domains = $this->domains();
        if (! $user || ! $user->contact_id || $domains === []) {
            return [];
        }

        $result = [];
        ContactAffiliation::active()
            ->where('contact_id', $user->contact_id)
            ->whereHas('type', fn ($query) => $query->whereIn('key', array_keys($domains)))
            ->with('type')
            ->get()
            ->sortBy(fn (ContactAffiliation $affiliation) => $affiliation->type->sort)
            ->each(function (ContactAffiliation $affiliation) use ($domains, &$result) {
                $domain = $domains[$affiliation->type->key];
                $result[$domain] = isset($result[$domain]) ? $result[$domain].', '.$affiliation->type->name : $affiliation->type->name;
            });

        return $result;
    }

    /**
     * The first domain the user may hold an address on, or null when not eligible.
     */
    public function domainFor(?User $user): ?string
    {
        return array_key_first($this->domainsFor($user));
    }

    public function eligible(?User $user): bool
    {
        return $this->domainsFor($user) !== [];
    }

    /**
     * How the address is called, e.g. on ID cards ("Gönüllü e-posta adresi").
     */
    public function label(): string
    {
        return $this->organization->get('mail_forwarding_label', 'Dernek e-posta adresi');
    }

    /**
     * @return array<string, string> affiliation type key => name, for the settings form
     */
    public function affiliationTypes(): array
    {
        return AffiliationType::orderBy('sort')->pluck('name', 'key')->all();
    }
}
