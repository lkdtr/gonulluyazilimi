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
        $emailRedirect = EmailRedirects::where('user_id', $event->user->id)->first();

        $usedByAnotherForwarding = EmailRedirects::where('email_forwarding', $event->newEmail)
            ->when($emailRedirect, fn ($query) => $query->where('id', '!=', $emailRedirect->id))
            ->exists();

        if ($usedByAnotherForwarding) {
            throw new ActionBlocked('İstenen e-posta adresi başka bir kayıtta kullanılıyor.');
        }

        if ($event->dryRun) {
            return;
        }

        if ($emailRedirect?->status === 1 && ! $this->postfixAdmin->updateAlias($emailRedirect->email_alias, $event->newEmail)) {
            throw new ActionBlocked('PostfixAdmin yönlendirmesi güncellenemedi; e-posta değişikliği uygulanmadı.');
        }
    }

    public function changed(UserEmailChanged $event): void
    {
        EmailRedirects::where('user_id', $event->user->id)->update(['email_forwarding' => $event->user->email]);
    }
}
