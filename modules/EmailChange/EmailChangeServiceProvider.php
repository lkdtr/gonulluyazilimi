<?php

namespace Modules\EmailChange;

use App\Modules\Menu;
use App\Modules\ModuleServiceProvider;
use App\Modules\Slots;

/**
 * Account e-mail change requests, applied after management approval.
 * Other modules follow the change through the UserEmailChanging/UserEmailChanged events.
 */
class EmailChangeServiceProvider extends ModuleServiceProvider
{
    protected function name(): string
    {
        return 'email-change';
    }

    protected function bootModule(Menu $menu, Slots $slots): void
    {
        $menu->add('user', 'account', 'E-posta değişikliği talebi', 'email-change-requests.create', [], 20);
        $menu->add('admin', 'email-change', 'E-posta değişikliği talepleri', 'admin.email-change-requests', [1], 50);
    }
}
