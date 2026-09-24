<?php

namespace Modules\MailForwarding\Listeners;

use App\Events\UserEmailChanged;
use App\Events\UserEmailChanging;
use App\Exceptions\ActionBlocked;
use Modules\MailForwarding\Models\EmailRedirects;
use Modules\MailForwarding\Support\PostfixAdminClient;

/**
 * Keeps the forwarding target in step with the user's account e-mail.
 */
class SyncForwardingWithAccountEmail
{
    public function __construct(private PostfixAdminClient $postfixAdmin)
    {
    }

    public function changing(UserEmailChanging $event): void
    {
        // The person's own addresses (one per domain) all point to their email.
        $usedByAnotherForwarding = EmailRedirects::where('email_forwarding', $event->newEmail)
            ->where('user_id', '!=', $event->user->id)
            ->exists();

        if ($usedByAnotherForwarding) {
            throw new ActionBlocked('İstenen e-posta adresi başka bir kayıtta kullanılıyor.');
        }

        if ($event->dryRun) {
            return;
        }

        foreach (EmailRedirects::where('user_id', $event->user->id)->where('status', 1)->get() as $emailRedirect) {
            if (! $this->postfixAdmin->updateAlias($emailRedirect->email_alias, $event->newEmail)) {
                throw new ActionBlocked('PostfixAdmin yönlendirmesi güncellenemedi ('.$emailRedirect->email_alias.'); e-posta değişikliği uygulanmadı.');
            }
        }
    }

    public function changed(UserEmailChanged $event): void
    {
        EmailRedirects::where('user_id', $event->user->id)->update(['email_forwarding' => $event->user->email]);
    }
}
