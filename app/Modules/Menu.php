<?php

namespace App\Modules;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Navigation items contributed by modules.
 *
 * Sections rendered by layouts.app: "user" (user operations dropdown) and
 * "admin" (manager operations dropdown). Items of the same group are rendered
 * together and groups are separated by a divider.
 */
class Menu
{
    private array $items = [];

    /**
     * @param  string  $label  translation key or plain text
     * @param  int[]  $roles  allowed user roles; empty means every signed-in user
     */
    public function add(string $section, string $group, string $label, string $route, array $roles = [], int $order = 100): void
    {
        $this->items[$section][] = compact('group', 'label', 'route', 'roles', 'order');
    }

    /**
     * Visible items of a section, grouped: [[item, item], [item], ...].
     */
    public function groups(string $section, ?Authenticatable $user): array
    {
        $items = array_filter(
            $this->items[$section] ?? [],
            fn (array $item) => $item['roles'] === [] || ($user && in_array((int) $user->role, $item['roles'], true))
        );

        usort($items, fn (array $a, array $b) => $a['order'] <=> $b['order']);

        $groups = [];
        foreach ($items as $item) {
            $groups[$item['group']][] = $item;
        }

        return array_values($groups);
    }
}
