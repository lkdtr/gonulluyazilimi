<?php

namespace App\Modules;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * Base class for module service providers.
 *
 * Layout of a module directory (all parts optional):
 *   <Name>ServiceProvider.php
 *   config.php               merged into config("<name>")
 *   routes/web.php           loaded inside the "web" middleware group
 *   resources/views          available as "<name>::view.name"
 *   database/migrations      loaded by ModulesServiceProvider for every module
 *
 * A module may use core (App\) classes and the classes of modules it lists in
 * "requires". Everything else goes through core events, menu items and slots.
 */
abstract class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Module key as used in config/modules.php, e.g. "mail-forwarding".
     */
    abstract protected function name(): string;

    /**
     * Register menu items, view slots and listeners of the module.
     */
    protected function bootModule(Menu $menu, Slots $slots): void
    {
    }

    public function register(): void
    {
        if (is_file($config = $this->modulePath('config.php'))) {
            $this->mergeConfigFrom($config, $this->name());
        }
    }

    public function boot(): void
    {
        if (is_dir($views = $this->modulePath('resources/views'))) {
            $this->loadViewsFrom($views, $this->name());
        }

        if (is_file($routes = $this->modulePath('routes/web.php')) && ! $this->app->routesAreCached()) {
            Route::middleware('web')->group($routes);
        }

        $this->bootModule($this->app->make(Menu::class), $this->app->make(Slots::class));
    }

    protected function modulePath(string $path = ''): string
    {
        $directory = dirname((new \ReflectionClass(static::class))->getFileName());

        return $path === '' ? $directory : $directory.DIRECTORY_SEPARATOR.$path;
    }
}
