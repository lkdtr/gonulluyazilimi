<?php

namespace Modules\Volunteer;

use App\Events\DashboardVisited;
use App\Modules\Menu;
use App\Modules\ModuleServiceProvider;
use App\Modules\Slots;
use App\Models\AffiliationType;
use App\Models\ContactAffiliation;
use App\Models\User;
use App\Modules\Dashboard;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Modules\Volunteer\Listeners\MarkAsVolunteer;
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

        // A member (active member affiliation) is not counted as a volunteer.
        $memberContacts = fn () => ContactAffiliation::active()->ofType(AffiliationType::MEMBER)->select('contact_id');
        $volunteers = fn () => User::where('status', 1)->whereNotIn('contact_id', $memberContacts());

        $this->dashboard()->stat('Gönüllü', 'users', fn () => $volunteers()->count(), 'admin.users', [1, 2], 10, 'Üyeler hariç');
        $this->dashboard()->stat('Son 30 günde katılan', 'user-plus', fn () => $volunteers()->where('created_at', '>=', now()->subDays(30))->count(), 'admin.users', [1, 2], 11);
        $this->dashboard()->chart('Toplam gönüllü', fn () => Dashboard::monthly($volunteers(), cumulative: true), 'line', [1, 2], 10, 'Son 12 ayın sonundaki gönüllü sayısı');
        $this->dashboard()->chart('Aylık yeni gönüllü', fn () => Dashboard::monthly($volunteers()), 'bar', [1, 2], 11, 'Son 12 ayda her ay kaydolan gönüllü sayısı');

        Event::listen(Registered::class, MarkAsVolunteer::class);
        Event::listen(DashboardVisited::class, SubscribeToVolunteerList::class);
    }
}
