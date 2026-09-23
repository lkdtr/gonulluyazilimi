<?php

namespace Modules\Reference;

use App\Modules\Menu;
use App\Modules\ModuleServiceProvider;
use App\Modules\Slots;

/**
 * Membership reference requests.
 */
class ReferenceServiceProvider extends ModuleServiceProvider
{
    protected function name(): string
    {
        return 'reference';
    }

    protected function bootModule(Menu $menu, Slots $slots): void
    {
        $menu->add('user', 'reference', 'panel.create_reference_request', 'create-reference-request', [], 40);
        $menu->add('admin', 'reference', 'panel.reference_requests', 'admin.reference-requests', [1, 2], 80);
    }
}
