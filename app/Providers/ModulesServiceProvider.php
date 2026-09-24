<?php

namespace App\Providers;

use App\Models\Cities;
use App\Models\Contact;
use App\Modules\ContactFields;
use App\Modules\Dashboard;
use App\Modules\Menu;
use App\Modules\ModuleManager;
use App\Modules\Permissions;
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
        $this->app->singleton(Dashboard::class);
        $this->app->singleton(Permissions::class, function () {
            $permissions = new Permissions();
            $permissions->group('panel', 'Yönetim paneli', 1);
            $permissions->register('admin.access', 'Yönetim paneline girebilsin', 'panel', 1);

            return $permissions;
        });
        $this->app->singleton(\App\Support\CustomFields::class);
        $this->app->singleton(ContactFields::class, function () {
            $fields = new ContactFields();
            $fields->register('first_name', 'Ad', fn (Contact $contact) => $contact->first_name, 1);
            $fields->register('last_name', 'Soyad', fn (Contact $contact) => $contact->last_name, 2);
            $fields->register('email', 'E-posta', fn (Contact $contact) => $contact->email, 10);
            $fields->register('phone', 'Telefon', fn (Contact $contact) => $contact->phone, 11);
            $fields->register('city', 'İl', fn (Contact $contact) => $contact->city_id ? Cities::where('city_plate_no', $contact->city_id)->value('city_name') : null, 12);
            // Until a membership module holds member numbers, the account's LKD
            // member number is the one known.
            $fields->register('member_number', 'Üye no', fn (Contact $contact) => $contact->user?->lkd_user_id > 0 ? $contact->user->lkd_user_id : null, 20);
            // Custom fields defined in the admin panel, e.g. to print on ID cards.
            $fields->resolver(function () {
                try {
                    return \App\Models\CustomField::active()->orderBy('sort')->get()->map(fn ($field) => [
                        'custom.'.$field->key,
                        $field->label,
                        fn (Contact $contact) => $field->display(\App\Models\CustomFieldValue::where('contact_id', $contact->id)->where('custom_field_id', $field->id)->value('value')),
                        100 + $field->sort,
                    ])->all();
                } catch (\Throwable) {
                    return [];
                }
            });

            return $fields;
        });

        $modules = $this->app->make(ModuleManager::class);

        foreach ($modules->enabledModules() as $name) {
            $this->app->register($modules->provider($name));
        }
    }

    public function boot(ModuleManager $modules): void
    {
        foreach (array_keys($modules->providers()) as $name) {
            $this->loadMigrationsFrom($modules->path($name, 'database/migrations'));
        }

        Blade::directive('moduleSlot', fn (string $expression) => "<?php echo app(\\App\\Modules\\Slots::class)->render({$expression}); ?>");
        Blade::if('module', fn (string $name) => $modules->enabled($name));
    }
}
