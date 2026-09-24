<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\ModuleManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * A new installation whose .env lists no MODULE_* variable runs the core and
 * the locked admin module only.
 */
class CoreOnlyInstallTest extends TestCase
{
    use RefreshDatabase;

    private array $saved = [];

    protected function setUp(): void
    {
        foreach (array_unique([...array_keys($_SERVER), ...array_keys($_ENV)]) as $name) {
            if (str_starts_with($name, 'MODULE_')) {
                $this->saved[$name] = [$_SERVER[$name] ?? null, $_ENV[$name] ?? null, getenv($name)];
                unset($_SERVER[$name], $_ENV[$name]);
                putenv($name);
            }
        }

        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        foreach ($this->saved as $name => [$server, $env, $process]) {
            if ($server !== null) {
                $_SERVER[$name] = $server;
            }
            if ($env !== null) {
                $_ENV[$name] = $env;
            }
            if ($process !== false) {
                putenv("{$name}={$process}");
            }
        }
    }

    public function test_only_the_admin_module_is_enabled(): void
    {
        $this->assertSame(['admin'], app(ModuleManager::class)->enabledModules());
    }

    public function test_nothing_volunteer_specific_shows_without_the_volunteer_module(): void
    {
        $owner = User::factory()->create(['role' => 1]);

        $this->get('/register')->assertOk()->assertDontSee('Gönüllü Ol');
        $response = $this->actingAs($owner)->get('/admin')->assertOk()->assertDontSee('Gönüllü')->assertDontSee('gönüllü');
        $this->assertNotNull(collect($response->viewData('stats'))->firstWhere('label', 'Kayıtlı hesap'));
        $this->assertNotNull(collect($response->viewData('stats'))->firstWhere('label', 'Üye'));
    }

    public function test_the_core_and_admin_panel_work_without_modules(): void
    {
        $owner = User::factory()->create(['role' => 1]);

        $this->get('/')->assertOk();
        $this->actingAs($owner)->get('/home')->assertOk();
        $this->actingAs($owner)->get('/admin')->assertOk();
        $this->actingAs($owner)->get('/admin/contacts')->assertOk();
        $this->actingAs($owner)->get('/admin/roles')->assertOk();

        $this->actingAs($owner)->get('/create-seminar-request')->assertNotFound();
        $this->actingAs($owner)->get('/email-redirects')->assertNotFound();
        $this->actingAs($owner)->get('/representations')->assertNotFound();
    }
}
