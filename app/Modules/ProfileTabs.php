<?php

namespace App\Modules;

/**
 * Tabs of the profile page ("Bilgilerim"). The core and modules add tabs;
 * each tab renders its view and the custom field groups it claims. Fields
 * of groups no tab claims appear on the first tab.
 */
class ProfileTabs
{
    private array $tabs = [];

    /**
     * @param  string[]  $groups  custom field groups shown on this tab
     */
    public function add(string $key, string $label, ?string $view = null, int $order = 100, array $groups = [], ?string $icon = null): void
    {
        $this->tabs[$key] = compact('key', 'label', 'view', 'order', 'groups', 'icon');
    }

    /**
     * Tabs in order: [['key', 'label', 'view', 'order', 'groups', 'icon'], ...].
     */
    public function all(): array
    {
        $tabs = array_values($this->tabs);
        usort($tabs, fn (array $a, array $b) => $a['order'] <=> $b['order']);

        return $tabs;
    }

    /**
     * The tab a custom field group is shown on.
     */
    public function tabForGroup(string $group): ?string
    {
        $tabs = $this->all();
        foreach ($tabs as $tab) {
            if (in_array($group, $tab['groups'], true)) {
                return $tab['key'];
            }
        }

        return $tabs[0]['key'] ?? null;
    }
}
