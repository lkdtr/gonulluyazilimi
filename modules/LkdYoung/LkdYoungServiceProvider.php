<?php

namespace Modules\LkdYoung;

use App\Modules\Menu;
use App\Modules\ModuleServiceProvider;
use App\Modules\Slots;

/**
 * LKD Genç: university participation, university representatives and their announcements.
 * Requires mail-forwarding: representatives need an active forwarding address.
 */
class LkdYoungServiceProvider extends ModuleServiceProvider
{
    protected function name(): string
    {
        return 'lkd-young';
    }

    protected function bootModule(Menu $menu, Slots $slots): void
    {
        $menu->add('user', 'community', 'panel.join_lkd_young', 'join-lkd-young', [], 50);
        $menu->add('admin', 'lkd-young', 'LKD Genç yönetimi', 'admin.lkd-young', [1], 60);

        $slots->push('welcome.sections', 'lkd-young::partials.welcome', 10);
    }
}
