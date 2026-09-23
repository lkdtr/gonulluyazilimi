<?php

namespace App\Modules;

use Illuminate\Support\HtmlString;

/**
 * Named places in core views where modules can render their own partials,
 * e.g. @moduleSlot('home.main') or @moduleSlot('admin.users.actions', ['user' => $user]).
 */
class Slots
{
    private array $views = [];

    public function push(string $slot, string $view, int $order = 100): void
    {
        $this->views[$slot][] = compact('view', 'order');
    }

    public function render(string $slot, array $data = []): HtmlString
    {
        $views = $this->views[$slot] ?? [];
        usort($views, fn (array $a, array $b) => $a['order'] <=> $b['order']);

        return new HtmlString(implode('', array_map(
            fn (array $entry) => view($entry['view'], $data)->render(),
            $views
        )));
    }
}
