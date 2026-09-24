<?php

namespace Modules\LkdYoung;

use App\Modules\Menu;
use App\Modules\ModuleServiceProvider;
use App\Modules\Slots;
use Modules\LkdYoung\Models\LkdYoungApplication;
use Modules\LkdYoung\Models\LkdYoungRepresentative;

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
        $menu->label('user', 'community', 'Topluluk', 'users-group');
        $menu->add('user', 'community', 'panel.join_lkd_young', 'join-lkd-young', [], 50);
        $menu->label('admin', 'lkd-young', null, 'school');
        $menu->add('admin', 'lkd-young', 'LKD Genç yönetimi', 'admin.lkd-young', [1], 60);

        $this->dashboard()->stat('LKD Genç üyesi', 'school', fn () => LkdYoungApplication::where('status', 'active')->count(), 'admin.lkd-young', [1], 60);
        $this->dashboard()->stat('Bekleyen LKD Genç temsilcisi', 'user-question', fn () => LkdYoungRepresentative::where('status', 'pending')->count(), 'admin.lkd-young', [1], 61);

        $slots->push('welcome.sections', 'lkd-young::partials.welcome', 10);

        // KVKK deletion: remove the personal data this module holds.
        \Illuminate\Support\Facades\Event::listen(\App\Events\ContactAnonymized::class, function (\App\Events\ContactAnonymized $event) {
            $event->userId && LkdYoungApplication::where('user_id', $event->userId)->delete();
        });

    }
}
