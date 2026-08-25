<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_mutations_do_not_accept_get_requests(): void
    {
        $owner = User::factory()->create(['role' => 1]);
        $user = User::factory()->create();

        $this->actingAs($owner)
            ->get('/set-manager-role/'.$user->id)
            ->assertMethodNotAllowed();

        $user->refresh();
        $this->assertSame(3, $user->role);
    }

    public function test_only_owners_can_run_tc_identity_checks(): void
    {
        $manager = User::factory()->create(['role' => 2]);
        $user = User::factory()->create();

        $this->actingAs($manager)
            ->post('/tc-kimlik-dogrula/'.$user->id)
            ->assertForbidden();
    }

    public function test_owner_can_change_a_users_role_with_a_patch_request(): void
    {
        $owner = User::factory()->create(['role' => 1]);
        $user = User::factory()->create(['role' => 3]);

        $this->actingAs($owner)
            ->patch('/set-manager-role/'.$user->id)
            ->assertRedirect(secure_url('/users'));

        $this->assertDatabaseHas('users', ['id' => $user->id, 'role' => 2]);
    }
}
