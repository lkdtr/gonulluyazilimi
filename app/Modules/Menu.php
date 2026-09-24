<?php

namespace App\Modules;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Navigation items contributed by the core and modules.
 *
 * Sections: "user" (site menu, layouts.app) and "admin" (admin panel menu,
 * layouts.admin). Both menus are horizontal: a group with a single visible
 * item is rendered as a link, a larger group as a dropdown titled with the
 * group label.
 */
class Menu
{
    private array $items = [];

    private array $labels = [];

    /**
     * @param  string  $label  translation key or plain text
     * @param  array<int|string>  $roles  legacy access levels (1 owner, 2 manager) and/or
     *                                    permission keys; empty means every signed-in user
     */
    public function add(string $section, string $group, string $label, string $route, array $roles = [], int $order = 100): void
    {
        $this->items[$section][] = compact('group', 'label', 'route', 'roles', 'order');
    }

    /**
     * Title and Tabler icon name (e.g. "users" for "ti ti-users") of a group.
     * The title is used for a dropdown and defaults to the first item's label.
     */
    public function label(string $section, string $group, ?string $label, ?string $icon = null): void
    {
        $this->labels[$section][$group] = compact('label', 'icon');
    }

    /**
     * Visible groups of a section in order: [['label' => ..., 'icon' => ..., 'items' => [...]], ...].
     */
    public function groups(string $section, ?Authenticatable $user): array
    {
        $items = array_filter(
            $this->items[$section] ?? [],
            fn (array $item) => $item['roles'] === [] || ($user && $user->canAccess($item['roles']))
        );

        usort($items, fn (array $a, array $b) => $a['order'] <=> $b['order']);

        $groups = [];
        foreach ($items as $item) {
            $groups[$item['group']]['items'][] = $item;
            $groups[$item['group']]['label'] ??= $this->labels[$section][$item['group']]['label'] ?? $item['label'];
            $groups[$item['group']]['icon'] ??= $this->labels[$section][$item['group']]['icon'] ?? 'point';
        }

        return array_values($groups);
    }
}
