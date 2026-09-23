<?php

namespace Modules\Representation;

use App\Events\ProfileUpdated;
use App\Modules\Menu;
use App\Modules\ModuleServiceProvider;
use App\Modules\Slots;
use Illuminate\Support\Facades\Event;
use Modules\Representation\Listeners\AskForContactConsent;

/**
 * City representations: contact sharing with volunteers, candidacies and representation announcements.
 */
class RepresentationServiceProvider extends ModuleServiceProvider
{
    protected function name(): string
    {
        return 'representation';
    }

    protected function bootModule(Menu $menu, Slots $slots): void
    {
        $menu->add('user', 'community', 'Temsilcilikler', 'representations.index', [], 51);
        $menu->add('user', 'community', 'Temsilci adayı ol', 'representations.candidate', [], 52);
        $menu->add('admin', 'representation', 'Temsilcilik yönetimi', 'admin.representations', [1], 70);

        $slots->push('welcome.sections', 'representation::partials.welcome', 20);

        Event::listen(ProfileUpdated::class, AskForContactConsent::class);
    }
}
