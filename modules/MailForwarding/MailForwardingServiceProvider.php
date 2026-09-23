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

        $menu->add('user', 'account', 'panel.email_forwarding', 'email-redirects', [], 10);

        $slots->push('home.top', 'mail-forwarding::partials.home-banner');
        $slots->push('admin.users.head', 'mail-forwarding::partials.users-head');
        $slots->push('admin.users.cell', 'mail-forwarding::partials.users-cell');
        $slots->push('admin.users.actions', 'mail-forwarding::partials.users-actions');

        View::composer('mail-forwarding::partials.home-banner', function ($view) {
            $view->with('email_redirect_is_exist', EmailRedirects::where('user_id', Auth::id())->first());
        });

        Event::listen(UserEmailChanging::class, [SyncForwardingWithAccountEmail::class, 'changing']);
        Event::listen(UserEmailChanged::class, [SyncForwardingWithAccountEmail::class, 'changed']);
    }
}
