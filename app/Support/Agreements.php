<?php

namespace App\Support;

use App\Models\Agreement;
use App\Models\AgreementAcceptance;
use App\Models\AgreementVersion;
use App\Models\User;
use Throwable;

/**
 * Agreements in force and their acceptance. A form shows the checkbox of an
 * agreement (<x-agreement-checkbox key="kvkk" />) only while a version is
 * published, requires it through rules() and records it with accept().
 */
class Agreements
{
    private array $current = [];

    public function current(string $key): ?AgreementVersion
    {
        if (! array_key_exists($key, $this->current)) {
            try {
                $this->current[$key] = Agreement::findByKey($key)?->currentVersion;
            } catch (Throwable) {
                // Before the agreement tables exist (fresh install, migrations).
                $this->current[$key] = null;
            }
        }

        return $this->current[$key];
    }

    /**
     * Validation rules of the checkbox covering the given agreements:
     * required while any of them is in force.
     *
     * @return string[]
     */
    public function rules(string ...$keys): array
    {
        foreach ($keys as $key) {
            if ($this->current($key)) {
                return ['required'];
            }
        }

        return ['nullable'];
    }

    /**
     * Record that the user accepted the versions in force of the agreements.
     */
    public function accept(User $user, string $context, string ...$keys): void
    {
        $request = request();

        foreach ($keys as $key) {
            if (! $version = $this->current($key)) {
                continue;
            }

            AgreementAcceptance::create([
                'agreement_version_id' => $version->id,
                'user_id' => $user->id,
                'contact_id' => $user->contact_id,
                'context' => $context,
                'ip' => $request?->ip(),
                'user_agent' => mb_substr((string) $request?->userAgent(), 0, 255) ?: null,
                'accepted_at' => now(),
            ]);
        }
    }

    public function forget(): void
    {
        $this->current = [];
    }
}
