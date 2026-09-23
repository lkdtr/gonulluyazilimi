<?php

namespace Modules\Announcements;

use App\Modules\Menu;
use App\Modules\ModuleServiceProvider;
use App\Modules\Slots;
use Illuminate\Support\Facades\View;
use Modules\Announcements\Models\Announcements;

/**
 * Announcements shown on the dashboard and optionally mailed to a Mailgun list.
 */
class AnnouncementsServiceProvider extends ModuleServiceProvider
{
    protected function name(): string
    {
        return 'announcements';
    }

    protected function bootModule(Menu $menu, Slots $slots): void
    {
        $menu->label('admin', 'announcements', 'panel.announcements', 'speakerphone');
        $menu->add('admin', 'announcements', 'panel.announcements', 'admin.announcements', [1, 2], 30);
        $menu->add('admin', 'announcements', 'panel.new_announcement', 'admin.announcements.create', [1, 2], 31);

        $slots->push('home.main', 'announcements::partials.home');

        View::composer('announcements::partials.home', function ($view) {
            $view->with('announcements', Announcements::where("status", 1)
                ->where('finished_at', '>', now())
                ->orderBy("id", "DESC")
                ->paginate(10));
        });
    }
}
