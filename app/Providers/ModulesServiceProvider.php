<?php

namespace App\Providers;

use App\Modules\Menu;
use App\Modules\ModuleManager;
use App\Modules\Slots;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ModulesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ModuleManager::class, fn ($app) => new ModuleManager($app['config']->get('modules.modules', [])));
        $this->app->singleton(Menu::class);
        $this->app->singleton(Slots::class);

        $modules = $this->app->make(ModuleManager::class);

        foreach ($modules->enabledModules() as $name) {
            $this->app->register($modules->provider($name));
        }
    }

    public function boot(ModuleManager $modules, Menu $menu): void
    {
        foreach (array_keys($modules->providers()) as $name) {
            $this->loadMigrationsFrom($modules->path($name, 'database/migrations'));
        }

        Blade::directive('moduleSlot', fn (string $expression) => "<?php echo app(\\App\\Modules\\Slots::class)->render({$expression}); ?>");
        Blade::if('module', fn (string $name) => $modules->enabled($name));

        $menu->add('admin', 'users', 'panel.users', 'users', [1, 2], 10);
        $menu->add('admin', 'process-logs', 'panel.process_logs', 'process-logs', [1, 2], 20);
    }
}
