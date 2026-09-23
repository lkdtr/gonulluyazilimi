<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModuleToggleTest extends TestCase
{
    use RefreshDatabase;

    private const DISABLED = ['MODULE_VOLUNTEER', 'MODULE_LKD_YOUNG'];

    protected function setUp(): void
    {
        foreach (self::DISABLED as $name) {
            $_ENV[$name] = $_SERVER[$name] = 'false';
        }

        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        foreach (self::DISABLED as $name) {
            unset($_ENV[$name], $_SERVER[$name]);
        }
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

        $this->actingAs($owner)->get('/users')
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
