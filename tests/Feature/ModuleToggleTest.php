<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModuleToggleTest extends TestCase
{
    use RefreshDatabase;

    private const DISABLED = ['MODULE_VOLUNTEER', 'MODULE_LKD_YOUNG'];

    private array $saved = [];

    protected function setUp(): void
    {
        foreach (self::DISABLED as $name) {
            $this->saved[$name] = [$_SERVER[$name] ?? null, $_ENV[$name] ?? null];
            $_ENV[$name] = $_SERVER[$name] = 'false';
        }

        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Put back the values phpunit.xml set, so later tests see the modules on.
        foreach ($this->saved as $name => [$server, $env]) {
            unset($_SERVER[$name], $_ENV[$name]);
            if ($server !== null) {
                $_SERVER[$name] = $server;
            }
            if ($env !== null) {
                $_ENV[$name] = $env;
            }
        }
    }

    public function test_mail_forwarding_is_off_when_no_enabled_module_needs_it(): void
    {
        // phpunit.xml does not set MODULE_MAIL_FORWARDING; only volunteer and lkd-young require it.
        $this->assertFalse(app(\App\Modules\ModuleManager::class)->enabled('mail-forwarding'));
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('email-redirects'));
    }

    public function test_disabled_modules_register_no_routes(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/email-redirects')->assertNotFound();
        $this->actingAs($user)->get('/create-reference-request')->assertNotFound();
        $this->actingAs($user)->get('/join-lkd-young')->assertNotFound();

        $this->actingAs($user)->get('/create-seminar-request')->assertOk();
        $this->actingAs($user)->get('/representations')->assertOk();
    }

    public function test_core_pages_render_without_the_disabled_modules(): void
    {
        $owner = User::factory()->create(['role' => 1]);

        $this->actingAs($owner)->get('/home')
            ->assertOk()
            ->assertDontSee('/email-redirects', false)
            ->assertSee(route('create-seminar-request'), false);

        $this->actingAs($owner)->get('/admin/users')
            ->assertOk()
            ->assertDontSee(trans('auth.alias'));

        $this->post('/logout');
        $this->get('/')
            ->assertOk()
            ->assertDontSee('Gönüllüsü Nedir?')
            ->assertDontSee('LKD Genç')
            ->assertSee(trans('auth.register'));
    }
}
