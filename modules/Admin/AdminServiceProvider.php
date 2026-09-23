<?php

namespace Modules\Admin;

use App\Modules\Menu;
use App\Modules\ModuleServiceProvider;
use App\Modules\Slots;

/**
 * Admin panel home, users and roles, identity checks and process logs.
 * Locked: every module adds its own pages to the panel.
 */
class AdminServiceProvider extends ModuleServiceProvider
{
    protected function name(): string
    {
        return 'admin';
    }

    protected function bootModule(Menu $menu, Slots $slots): void
    {
        $menu->label('admin', 'users', 'panel.users');
        $menu->add('admin', 'users', 'panel.users', 'admin.users', [1, 2], 10);
        $menu->add('admin', 'users', 'panel.process_logs', 'admin.process-logs', [1], 11);
    }
}
