<?php

namespace Database\Seeders;

use App\Models\Agreement;
use App\Models\AgreementAcceptance;
use App\Models\User;
use App\Support\HtmlSanitizer;
use Illuminate\Database\Seeder;

/**
 * LKD's agreements as they were before they became editable: the privacy
 * policy and the email usage terms, published as version 1. Accounts that
 * accepted the privacy policy on registration (users.agreement_at) get that
 * acceptance recorded against version 1. Agreements that already exist are
 * left alone.
 */
class LkdAgreementSeeder extends Seeder
{
    private const AGREEMENTS = [
        Agreement::PRIVACY => 'Kişisel Verilerin Korunması ve İşlenmesi Politikası',
        Agreement::EMAIL_USAGE => 'E-Posta Kullanım Sözleşmesi',
    ];

    public function run(HtmlSanitizer $sanitizer): void
    {
        foreach (self::AGREEMENTS as $key => $title) {
            if (Agreement::where('key', $key)->exists()) {
                continue;
            }

            $agreement = Agreement::create(['key' => $key, 'title' => $title]);
            $version = $agreement->versions()->create([
                'version' => 1,
                'content' => $sanitizer->sanitizePage(file_get_contents(__DIR__."/lkd/agreements/{$key}.html")),
            ]);
            $version->forceFill(['published_at' => now()])->save();

            if ($key === Agreement::PRIVACY) {
                User::whereNotNull('agreement_at')->select(['id', 'contact_id', 'agreement_at'])->chunkById(500, function ($users) use ($version) {
                    AgreementAcceptance::insert($users->map(fn (User $user) => [
                        'agreement_version_id' => $version->id,
                        'user_id' => $user->id,
                        'contact_id' => $user->contact_id,
                        'context' => 'register',
                        'accepted_at' => $user->agreement_at,
                    ])->all());
                });
            }
        }
    }
}
