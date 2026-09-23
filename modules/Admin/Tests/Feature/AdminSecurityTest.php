<?php

namespace Modules\Admin\Tests\Feature;

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
            ->get('/admin/users/'.$user->id.'/manager-role')
            ->assertMethodNotAllowed();

        $user->refresh();
        $this->assertSame(3, $user->role);
    }

    public function test_only_owners_can_run_tc_identity_checks(): void
    {
        $manager = User::factory()->create(['role' => 2]);
        $user = User::factory()->create();

        $this->actingAs($manager)
            ->post('/admin/users/'.$user->id.'/tc-kimlik')
            ->assertForbidden();
    }

    public function test_owner_can_change_a_users_role_with_a_patch_request(): void
    {
        $owner = User::factory()->create(['role' => 1]);
        $user = User::factory()->create(['role' => 3]);

        $this->actingAs($owner)
            ->patch('/admin/users/'.$user->id.'/manager-role')
            ->assertRedirect(route('admin.users'));

        $this->assertDatabaseHas('users', ['id' => $user->id, 'role' => 2]);
    }

    public function test_admin_panel_is_only_for_owners_and_managers(): void
    {
        $this->get('/admin')->assertRedirect('/login');

        $this->actingAs(User::factory()->create(['role' => 3]))->get('/admin')->assertForbidden();

        $this->actingAs(User::factory()->create(['role' => 2]))->get('/admin')
            ->assertOk()
            ->assertSee(route('admin.users'), false)
            ->assertDontSee(route('admin.process-logs'), false);
    }

    public function test_owner_only_admin_pages_are_forbidden_to_managers(): void
    {
        $manager = User::factory()->create(['role' => 2]);

        $this->actingAs($manager)->get('/admin/users')->assertOk();
        $this->actingAs($manager)->get('/admin/process-logs')->assertForbidden();
        $this->actingAs($manager)->get('/admin/seminar-requests')->assertForbidden();
    }

    public function test_site_and_admin_menus_are_separate(): void
    {
        $owner = User::factory()->create(['role' => 1]);

        $this->actingAs($owner)->get('/home')
            ->assertOk()
            ->assertSee(route('admin.dashboard'), false)
            ->assertSee(route('create-seminar-request'), false)
            ->assertDontSee(route('admin.users'), false);

        $this->actingAs($owner)->get('/admin/users')
            ->assertOk()
            ->assertSee(route('admin.announcements'), false)
            ->assertSee(route('admin.seminar-subjects'), false)
            ->assertDontSee(route('create-seminar-request'), false);
    }

    public function test_owner_can_open_another_users_profile_in_the_admin_panel(): void
    {
        $owner = User::factory()->create(['role' => 1]);
        $user = User::factory()->create();

        $this->actingAs($owner)->get(route('admin.users.show', $user->id))
            ->assertOk()
            ->assertSee($user->email);
    }

    public function test_old_admin_addresses_redirect_to_the_admin_panel(): void
    {
        $owner = User::factory()->create(['role' => 1]);

        foreach ([
            '/users' => '/admin/users',
            '/process-logs' => '/admin/process-logs',
            '/user-infos/5' => '/admin/users/5',
            '/announcements' => '/admin/announcements',
            '/new-announcement' => '/admin/announcements/create',
            '/edit-announcement/7' => '/admin/announcements/7/edit',
            '/seminar-subjects' => '/admin/seminar-subjects',
            '/new-seminar-subject' => '/admin/seminar-subjects/create',
            '/edit-seminar-subject/3' => '/admin/seminar-subjects/3/edit',
            '/reference-requests' => '/admin/reference-requests',
        ] as $old => $new) {
            $this->actingAs($owner)->get($old)->assertStatus(301)->assertRedirect($new);
        }
    }
}
