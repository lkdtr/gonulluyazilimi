<?php

namespace Modules\MailForwarding;

use App\Events\UserEmailChanged;
use App\Events\UserEmailChanging;
use App\Models\User;
use App\Modules\Menu;
use App\Modules\ModuleServiceProvider;
use App\Modules\Slots;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Modules\MailForwarding\Listeners\SyncForwardingWithAccountEmail;
use Modules\MailForwarding\Models\EmailRedirects;
use Modules\MailForwarding\Support\ForwardingPolicy;

/**
 * E-mail forwarding addresses (name.surname@<domain>) managed on PostfixAdmin.
 */
class MailForwardingServiceProvider extends ModuleServiceProvider
{
    protected function name(): string
    {
        return 'mail-forwarding';
    }

    protected function bootModule(Menu $menu, Slots $slots): void
    {
        User::resolveRelationUsing('activeEmailRedirect', fn (User $user) => $user->hasOne(EmailRedirects::class)->where('status', 1));

        if ($this->app->runningInConsole()) {
            $this->commands([Console\aliasTest::class, Console\emailTest::class]);
        }

        $menu->label('user', 'account', 'E-posta', 'mail');
        $menu->add('user', 'account', 'panel.email_forwarding', 'email-redirects', [], 10, fn ($user) => app(ForwardingPolicy::class)->eligible($user));

        $this->permissions()->group('mail-forwarding', 'E-posta yönlendirme', 50);
        $this->permissions()->register('forwarding.manage', 'Dernek e-posta adresinin kimlere verileceğini ayarlayabilsin', 'mail-forwarding', 50);
        $menu->label('admin', 'settings', 'Ayarlar', 'settings');
        $menu->add('admin', 'settings', 'E-posta yönlendirme', 'admin.forwarding.settings', ['forwarding.manage'], 92);

        $this->contactFields()->register('forwarding_email', app(ForwardingPolicy::class)->label(), fn ($contact) => $contact->user?->activeEmailRedirect?->email_alias, 15);

        $this->dashboard()->stat('Aktif e-posta yönlendirmesi', 'mail-forward', fn () => EmailRedirects::where('status', 1)->count(), null, [1, 2], 13, implode(', ', array_map(fn ($domain) => '@'.$domain, array_unique(app(ForwardingPolicy::class)->domains()))));

        $slots->push('home.top', 'mail-forwarding::partials.home-banner');
        $slots->push('admin.users.head', 'mail-forwarding::partials.users-head');
        $slots->push('admin.users.cell', 'mail-forwarding::partials.users-cell');
        $slots->push('admin.users.actions', 'mail-forwarding::partials.users-actions');

        View::composer('mail-forwarding::partials.home-banner', function ($view) {
            $view->with('forwarding_eligible', app(ForwardingPolicy::class)->eligible(Auth::user()));
            $view->with('email_redirect_is_exist', EmailRedirects::where('user_id', Auth::id())->first());
        });

        Event::listen(UserEmailChanging::class, [SyncForwardingWithAccountEmail::class, 'changing']);
        Event::listen(UserEmailChanged::class, [SyncForwardingWithAccountEmail::class, 'changed']);

        // KVKK deletion: remove the personal data this module holds.
        \Illuminate\Support\Facades\Event::listen(\App\Events\ContactAnonymized::class, function (\App\Events\ContactAnonymized $event) {
            if ($event->userId === null) {
                return;
            }
            // The alias on the mail server is not touched here: the deletion
            // screen warns to remove it there first.
            EmailRedirects::where('user_id', $event->userId)->get()->each(fn (EmailRedirects $redirect) => $redirect->forceFill([
                'status' => 0,
                'email_alias' => 'silinmis-'.$redirect->id.'@invalid.invalid',
                'email_forwarding' => 'silinmis-'.$redirect->id.'@invalid.invalid',
            ])->save());
        });

        $slots->push('admin.data-deletion.notes', 'mail-forwarding::partials.data-deletion-note');

    }
}
