<?php

namespace Modules\IdCard;

use App\Models\AffiliationType;
use App\Modules\Menu;
use App\Modules\ModuleServiceProvider;
use App\Modules\Slots;
use Modules\IdCard\Models\IdCard;
use Modules\IdCard\Models\IdCardTemplate;

/**
 * Virtual ID cards: one card per affiliation (volunteer, member, board...)
 * with a design per affiliation type, the approved profile photo and a QR
 * code that opens a public verification page.
 */
class IdCardServiceProvider extends ModuleServiceProvider
{
    protected function name(): string
    {
        return 'id-card';
    }

    protected function bootModule(Menu $menu, Slots $slots): void
    {
        AffiliationType::resolveRelationUsing('idCardTemplate', fn (AffiliationType $type) => $type->hasOne(IdCardTemplate::class));

        $this->permissions()->group('id-cards', 'Kimlik kartları', 40);
        $this->permissions()->register('id-cards.manage', 'Kart şablonlarını düzenleyebilsin, kartları iptal edebilsin', 'id-cards', 40);

        $slots->push('account.menu', 'id-card::partials.account-menu', 20);

        $menu->label('admin', 'id-card', 'Kimlik kartları', 'id-badge-2');
        $menu->add('admin', 'id-card', 'Kart şablonları', 'admin.id-cards', ['id-cards.manage'], 40);
        $menu->add('admin', 'id-card', 'Verilen kartlar', 'admin.id-cards.issued', ['id-cards.manage'], 41);

        $this->dashboard()->stat('Verilen kimlik kartı', 'id-badge-2', fn () => IdCard::whereNull('revoked_at')->count(), 'admin.id-cards.issued', ['id-cards.manage'], 45);
    }
}
