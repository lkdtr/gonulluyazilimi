<?php

namespace Modules\Admin\Tests\Feature;

use App\Models\AffiliationType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_levels_become_system_roles(): void
    {
        $owner = User::factory()->create(['role' => 1]);
        $manager = User::factory()->create(['role' => 2]);
        $user = User::factory()->create();

        $this->assertTrue($owner->isOwner());
        $this->assertTrue($owner->hasPermission('roles.manage'));
        $this->assertSame(2, $manager->accessLevel());
        $this->assertTrue($manager->hasPermission('contacts.view'));
        $this->assertFalse($manager->hasPermission('contacts.manage'));
        $this->assertSame(3, $user->accessLevel());

        $manager->setAccessLevel(3);
        $this->assertSame(3, $manager->fresh()->accessLevel());
        $this->assertSame(0, $manager->roles()->count());
    }

    public function test_a_board_affiliation_grants_its_template_roles_while_it_lasts(): void
    {
        $board = AffiliationType::findByKey('board');
        $board->roles()->attach(Role::findByKey('manager'));
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin')->assertForbidden();

        $user->contact->affiliate('board');
        $this->actingAs($user->fresh())->get('/admin')->assertOk();

        $user->contact->endAffiliation('board');
        $this->actingAs($user->fresh())->get('/admin')->assertForbidden();
    }

    public function test_a_role_is_created_with_known_permissions_only(): void
    {
        $this->actingAs(User::factory()->create(['role' => 1]));

        $this->post('/admin/roles', ['key' => 'finance', 'name' => 'Mali işler', 'permissions' => ['admin.access', 'contacts.view']])
            ->assertRedirect(route('admin.roles'));
        $this->assertSame(['admin.access', 'contacts.view'], Role::findByKey('finance')->permissions());

        $this->post('/admin/roles', ['key' => 'bad', 'name' => 'Kötü', 'permissions' => ['everything']])
            ->assertSessionHasErrors('permissions.0');
    }

    public function test_system_roles_cannot_be_deleted(): void
    {
        $this->actingAs(User::factory()->create(['role' => 1]));

        $this->delete('/admin/roles/'.Role::findByKey('manager')->id)->assertSessionHas('danger-status');
        $this->assertNotNull(Role::findByKey('manager'));
    }

    public function test_the_roles_page_is_for_role_managers_only(): void
    {
        $this->actingAs(User::factory()->create(['role' => 2]))->get('/admin/roles')->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 1]))->get('/admin/roles')->assertOk()->assertSee('Sahip');
    }

    public function test_affiliation_types_are_managed_with_a_role_template(): void
    {
        $this->actingAs(User::factory()->create(['role' => 1]));
        $manager = Role::findByKey('manager');

        $this->post('/admin/affiliation-types', ['key' => 'youth-board', 'name' => 'Gençlik Kurulu Üyesi', 'sort' => 60, 'has_term' => '1', 'roles' => [$manager->id]])
            ->assertRedirect(route('admin.affiliation-types'));

        $type = AffiliationType::findByKey('youth-board');
        $this->assertTrue($type->has_term);
        $this->assertSame([$manager->id], $type->roles()->pluck('roles.id')->all());

        $this->get('/admin/affiliation-types')->assertOk()->assertSee('Gençlik Kurulu Üyesi');

        $this->delete('/admin/affiliation-types/'.AffiliationType::findByKey('member')->id)->assertSessionHas('danger-status');
        $this->delete("/admin/affiliation-types/{$type->id}")->assertSessionHas('success-status');
    }

    public function test_only_owners_put_the_owner_role_in_a_template(): void
    {
        $role = Role::create(['key' => 'settings', 'name' => 'Ayarlar']);
        $role->syncPermissions(['admin.access', 'affiliations.manage', 'roles.manage']);
        $user = User::factory()->create();
        $user->roles()->attach($role);
        $board = AffiliationType::findByKey('board');

        $this->actingAs($user)->put("/admin/affiliation-types/{$board->id}", [
            'name' => $board->name, 'sort' => $board->sort, 'roles' => [Role::findByKey('owner')->id, $role->id],
        ])->assertRedirect();

        $this->assertSame([$role->id], $board->roles()->pluck('roles.id')->all());
    }
}
