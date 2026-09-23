<?php

namespace Modules\Volunteer;

use App\Events\DashboardVisited;
use App\Modules\Menu;
use App\Modules\ModuleServiceProvider;
use App\Modules\Slots;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Modules\Volunteer\Listeners\SubscribeToVolunteerList;

/**
 * The volunteer programme: volunteer landing texts, "Gönüllü Ol" registration
 * label and the volunteer mailing list. Features only volunteers get are
 * separate modules listed in its "requires" (config/modules.php).
 */
class VolunteerServiceProvider extends ModuleServiceProvider
{
    protected function name(): string
    {
        return 'volunteer';
    }

    protected function bootModule(Menu $menu, Slots $slots): void
    {
        config(['app.register_label' => 'auth.become_a_volunteer']);

        $slots->push('welcome.intro', 'volunteer::partials.welcome');

        View::composer('welcome', fn ($view) => $view->with('title', 'Linux Kullanıcıları Derneği Gönüllüsü Nedir?'));

        Event::listen(DashboardVisited::class, SubscribeToVolunteerList::class);
    }
}
