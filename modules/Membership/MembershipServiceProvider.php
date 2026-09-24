<?php

namespace Modules\Membership;

use App\Events\ContactAnonymized;
use App\Models\Contact;
use App\Modules\Menu;
use App\Modules\ModuleServiceProvider;
use App\Modules\Slots;
use Illuminate\Support\Facades\Event;
use Modules\Membership\Models\Membership;

/**
 * Membership records: member number, status (applicant, member, suspended,
 * left), dates and history. An active membership holds the core "member"
 * affiliation. The member number replaces users.lkd_user_id.
 */
class MembershipServiceProvider extends ModuleServiceProvider
{
    protected function name(): string
    {
        return 'membership';
    }

    protected function bootModule(Menu $menu, Slots $slots): void
    {
        $this->permissions()->group('membership', 'Üyelik', 20);
        $this->permissions()->register('memberships.view', 'Üyeleri görebilsin', 'membership', 20);
        $this->permissions()->register('memberships.manage', 'Üye yapabilsin, üyelik durumunu ve üye no değiştirebilsin', 'membership', 21);

        $menu->label('admin', 'membership', 'Üyelik', 'id');
        $menu->add('admin', 'membership', 'Üyeler', 'admin.memberships', ['memberships.view'], 20);

        $slots->push('admin.contacts.show', 'membership::partials.contact-card', 10);
        $this->profileTabs()->add('membership', 'Üyelik', 'membership::partials.profile', 30, ['membership'], 'id');

        $this->customFields()->group('membership', 'Üyelik', 25);

        // The member number now comes from the membership record.
        $this->contactFields()->register('member_number', 'Üye no', fn (Contact $contact) => Membership::where('contact_id', $contact->id)->value('number'), 20);

        $this->dashboard()->stat('Aktif üye', 'id', fn () => Membership::active()->count(), 'admin.memberships', ['memberships.view'], 9);

        // KVKK deletion: the membership record stays (the association must
        // keep its member register) but ends; the contact is anonymized by the core.
        Event::listen(ContactAnonymized::class, function (ContactAnonymized $event) {
            Membership::where('contact_id', $event->contact->id)->get()->each(fn (Membership $membership) => $membership->forceFill([
                'status' => $membership->isActive() ? Membership::LEFT : $membership->status,
                'left_at' => $membership->left_at ?? today(),
                'notes' => null,
            ])->saveQuietly());
        });
    }
}
