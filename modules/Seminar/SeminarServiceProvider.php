<?php

namespace Modules\Seminar;

use App\Modules\Dashboard;
use App\Modules\Menu;
use App\Modules\ModuleServiceProvider;
use App\Modules\Slots;
use Modules\Seminar\Models\SeminarOffers;
use Modules\Seminar\Models\SeminarRequests;
use Modules\Seminar\Models\SeminarSubjects;

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
        $menu->label('user', 'seminar', 'Seminerler', 'presentation');
        $menu->add('user', 'seminar', 'panel.create_seminar_request', 'create-seminar-request', [], 30);
        $menu->add('user', 'seminar', 'Seminer vermek istiyorum', 'create-seminar-offer', [], 31);

        $menu->label('admin', 'seminar', 'Seminerler', 'presentation');
        $menu->add('admin', 'seminar', 'panel.seminar_subjects', 'admin.seminar-subjects', [1, 2], 40);
        $menu->add('admin', 'seminar', 'panel.new_seminar_subject', 'admin.seminar-subjects.create', [1, 2], 41);
        $menu->add('admin', 'seminar', 'panel.seminar_requests', 'admin.seminar-requests', [1, 2], 42);
        $menu->add('admin', 'seminar', 'Seminer verme başvuruları', 'admin.seminar-offers', [1], 43);

        $this->dashboard()->stat('Verilebilir seminer', 'presentation', fn () => SeminarSubjects::where('status', 1)->count(), 'admin.seminar-subjects', [1, 2], 30, 'Seminer havuzundaki konu');
        $this->dashboard()->stat('Bekleyen seminer talebi', 'calendar-question', fn () => SeminarRequests::where('status', 'pending')->count(), 'admin.seminar-requests', [1], 31);
        $this->dashboard()->stat('Seminer verme başvurusu', 'microphone', fn () => SeminarOffers::where('status', 'pending')->count(), 'admin.seminar-offers', [1], 32, 'Değerlendirme bekleyen');
        $this->dashboard()->chart('Aylık seminer talebi', fn () => Dashboard::monthly(SeminarRequests::query()), 'bar', [1], 30, 'Son 12 ayda her ay gelen seminer talebi');

        $slots->push('welcome.sections', 'seminar::partials.welcome', 30);
    }
}
