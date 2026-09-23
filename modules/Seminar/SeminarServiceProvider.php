<?php

namespace Modules\Seminar;

use App\Modules\Menu;
use App\Modules\ModuleServiceProvider;
use App\Modules\Slots;

/**
 * Seminar subjects, seminar requests from organizations and seminar offers from speakers.
 */
class SeminarServiceProvider extends ModuleServiceProvider
{
    protected function name(): string
    {
        return 'seminar';
    }

    protected function bootModule(Menu $menu, Slots $slots): void
    {
        $menu->add('user', 'seminar', 'panel.create_seminar_request', 'create-seminar-request', [], 30);
        $menu->add('user', 'seminar', 'Seminer vermek istiyorum', 'create-seminar-offer', [], 31);

        $menu->add('admin', 'seminar', 'panel.seminar_subjects', 'seminar-subjects', [1, 2], 40);
        $menu->add('admin', 'seminar', 'panel.new_seminar_subject', 'new-seminar-subject', [1, 2], 41);
        $menu->add('admin', 'seminar', 'panel.seminar_requests', 'admin.seminar-requests', [1, 2], 42);
        $menu->add('admin', 'seminar', 'Seminer verme başvuruları', 'admin.seminar-offers', [1], 43);

        $slots->push('welcome.sections', 'seminar::partials.welcome', 30);
    }
}
