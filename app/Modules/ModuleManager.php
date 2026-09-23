<?php

namespace App\Modules;

use InvalidArgumentException;

class ModuleManager
{
    /** @var array<string, array{enabled?: mixed, provider: class-string, requires?: string[]}> */
    private array $modules;

    /** @var string[] */
    private array $enabled;

    public function __construct(array $modules)
    {
        $this->modules = $modules;
        $this->enabled = $this->resolveEnabled();
    }

    public function enabled(string $name): bool
    {
        return in_array($name, $this->enabled, true);
    }

    /**
     * Enabled module names in configuration order.
     *
     * @return string[]
     */
    public function enabledModules(): array
    {
        return $this->enabled;
    }

    /**
     * Service provider classes of every configured module, enabled or not.
     *
     * @return array<string, class-string>
     */
    public function providers(): array
    {
        return array_map(fn (array $module) => $module['provider'], $this->modules);
    }

    public function provider(string $name): string
    {
        return $this->modules[$name]['provider'];
    }

    /**
     * Base directory of a module, derived from its service provider location.
     */
    public function path(string $name, string $path = ''): string
    {
        $directory = dirname((new \ReflectionClass($this->provider($name)))->getFileName());

        return $path === '' ? $directory : $directory.DIRECTORY_SEPARATOR.$path;
    }

    private function resolveEnabled(): array
    {
        $enabled = [];

        $enable = function (string $name) use (&$enable, &$enabled) {
            if (! isset($this->modules[$name])) {
                throw new InvalidArgumentException("Unknown module [{$name}].");
            }

            if (isset($enabled[$name])) {
                return;
            }

            $enabled[$name] = true;

            foreach ($this->modules[$name]['requires'] ?? [] as $dependency) {
                $enable($dependency);
            }
        };

        foreach ($this->modules as $name => $module) {
            if (filter_var($module['enabled'] ?? false, FILTER_VALIDATE_BOOL)) {
                $enable($name);
            }
        }

        return array_values(array_filter(array_keys($this->modules), fn ($name) => isset($enabled[$name])));
    }
}
