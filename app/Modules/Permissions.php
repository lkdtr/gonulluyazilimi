<?php

namespace App\Modules;

/**
 * Permission keys known to the application, registered by the core and the
 * modules. Roles store keys from this catalogue; the owner role holds all.
 */
class Permissions
{
    private array $permissions = [];

    private array $groups = [];

    public function group(string $group, string $label, int $order = 100): void
    {
        $this->groups[$group] = compact('label', 'order');
    }

    public function register(string $key, string $label, string $group, int $order = 100): void
    {
        $this->permissions[$key] = compact('key', 'label', 'group', 'order');
    }

    public function has(string $key): bool
    {
        return isset($this->permissions[$key]);
    }

    /**
     * @return string[]
     */
    public function keys(): array
    {
        return array_keys($this->permissions);
    }

    /**
     * Groups in order: [['label' => ..., 'permissions' => [['key' => ..., 'label' => ...], ...]], ...].
     */
    public function grouped(): array
    {
        $permissions = array_values($this->permissions);
        usort($permissions, fn (array $a, array $b) => $a['order'] <=> $b['order']);

        $groups = [];
        foreach ($permissions as $permission) {
            $group = $permission['group'];
            $groups[$group]['label'] ??= $this->groups[$group]['label'] ?? $group;
            $groups[$group]['order'] ??= $this->groups[$group]['order'] ?? 100;
            $groups[$group]['permissions'][] = $permission;
        }

        uasort($groups, fn (array $a, array $b) => $a['order'] <=> $b['order']);

        return array_values($groups);
    }
}
