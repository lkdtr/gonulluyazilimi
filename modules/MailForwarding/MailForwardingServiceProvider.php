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
        // A person may hold one address per domain (e.g. volunteer and member).
        User::resolveRelationUsing('activeEmailRedirects', fn (User $user) => $user->hasMany(EmailRedirects::class)->where('status', 1)->orderBy('id'));

        if ($this->app->runningInConsole()) {
            $this->commands([Console\aliasTest::class, Console\emailTest::class]);
        }

        $menu->label('user', 'account', 'E-posta', 'mail');
        $menu->add('user', 'account', 'panel.email_forwarding', 'email-redirects', [], 10, fn ($user) => app(ForwardingPolicy::class)->eligible($user));

        $this->permissions()->group('mail-forwarding', 'E-posta yönlendirme', 50);
        $this->permissions()->register('forwarding.manage', 'Dernek e-posta adresinin kimlere verileceğini ayarlayabilsin', 'mail-forwarding', 50);
        $menu->label('admin', 'settings', 'Ayarlar', 'settings');
        $menu->add('admin', 'settings', 'E-posta yönlendirme', 'admin.forwarding.settings', ['forwarding.manage'], 92);

        $this->contactFields()->register('forwarding_email', app(ForwardingPolicy::class)->label(), fn ($contact) => $contact->user?->activeEmailRedirects->pluck('email_alias')->implode(', ') ?: null, 15);
        // One field per domain, so a volunteer card can show @penguen.org.tr and a member card @linux.org.tr.
        $this->contactFields()->resolver(fn () => collect(app(ForwardingPolicy::class)->domains())->unique()->values()->map(fn ($domain, $i) => [
            'forwarding_email.'.$domain,
            'E-posta adresi (@'.$domain.')',
            fn ($contact) => $contact->user?->activeEmailRedirects->firstWhere('domain', $domain)?->email_alias,
            16 + $i,
        ])->all());

        $this->dashboard()->stat('Aktif e-posta yönlendirmesi', 'mail-forward', fn () => EmailRedirects::where('status', 1)->count(), null, [1, 2], 13, implode(', ', array_map(fn ($domain) => '@'.$domain, array_unique(app(ForwardingPolicy::class)->domains()))));

        $slots->push('home.top', 'mail-forwarding::partials.home-banner');
        $slots->push('admin.users.head', 'mail-forwarding::partials.users-head');
        $slots->push('admin.users.cell', 'mail-forwarding::partials.users-cell');
        $slots->push('admin.users.actions', 'mail-forwarding::partials.users-actions');

        View::composer('mail-forwarding::partials.home-banner', function ($view) {
            // Point to the first domain the person may still set up.
            $policy = app(ForwardingPolicy::class);
            $redirects = EmailRedirects::where('user_id', Auth::id())->get()->keyBy('domain');
            $pending = collect($policy->domainsFor(Auth::user()))->keys()->first(fn ($domain) => ($redirects[$domain]->status ?? 0) != 1);
            $view->with('email_redirect_is_exist', $pending === null ? $redirects->first() : ($redirects[$pending] ?? null));
            $view->with('forwarding_eligible', $pending !== null || $redirects->isNotEmpty());
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
                // A distinct fake domain per row keeps (user, domain) unique.
                'email_alias' => 'silinmis@'.$redirect->id.'.invalid',
                'email_forwarding' => 'silinmis@'.$redirect->id.'.invalid',
            ])->save());
        });

        $slots->push('admin.data-deletion.notes', 'mail-forwarding::partials.data-deletion-note');

    }
}
