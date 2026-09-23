<?php

namespace Modules\Admin;

use App\Models\User;
use App\Modules\Dashboard;
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
        $menu->label('admin', 'dashboard', null, 'layout-dashboard');
        $menu->add('admin', 'dashboard', 'Genel Bakış', 'admin.dashboard', [1, 2], 1);

        $menu->label('admin', 'users', 'panel.users', 'users');
        $menu->add('admin', 'users', 'panel.users', 'admin.users', [1, 2], 10);
        $menu->add('admin', 'users', 'panel.process_logs', 'admin.process-logs', [1], 11);

        $volunteers = fn () => User::where('status', 1);

        $this->dashboard()->stat('Gönüllü', 'users', fn () => $volunteers()->count(), 'admin.users', [1, 2], 10);
        $this->dashboard()->stat('Son 30 günde katılan', 'user-plus', fn () => $volunteers()->where('created_at', '>=', now()->subDays(30))->count(), 'admin.users', [1, 2], 11);
        $this->dashboard()->chart('Toplam gönüllü', fn () => Dashboard::monthly($volunteers(), cumulative: true), 'line', [1, 2], 10, 'Son 12 ayın sonundaki gönüllü sayısı');
        $this->dashboard()->chart('Aylık yeni gönüllü', fn () => Dashboard::monthly($volunteers()), 'bar', [1, 2], 11, 'Son 12 ayda her ay kaydolan gönüllü sayısı');
    }
}
