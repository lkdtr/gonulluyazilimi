<?php

namespace Modules\Admin\Tests\Feature;

use App\Models\AffiliationType;
use App\Models\Contact;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactAdminTest extends TestCase
{
    use RefreshDatabase;

    private function owner(): User
    {
        return User::factory()->create(['role' => 1]);
    }

    /**
     * An account whose only role is a custom one with the given permissions.
     */
    private function userWith(array $permissions): User
    {
        $role = Role::create(['key' => 'custom-'.count($permissions), 'name' => 'Özel']);
        $role->syncPermissions($permissions);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        return $user;
    }

    public function test_contacts_are_listed_and_filtered_by_affiliation(): void
    {
        $member = Contact::create(['first_name' => 'Ada', 'last_name' => 'Lovelace']);
        $member->affiliate('member');
        Contact::create(['type' => 'organization', 'organization_name' => 'Pardus Topluluğu']);

        $this->actingAs($this->owner())->get('/admin/contacts')
            ->assertOk()
            ->assertSee('Ada Lovelace')
            ->assertSee('Pardus Topluluğu');

        $this->get('/admin/contacts?affiliation=member')
            ->assertOk()
            ->assertSee('Ada Lovelace')
            ->assertDontSee('Pardus Topluluğu');

        $this->get('/admin/contacts?q=pardus')
            ->assertSee('Pardus Topluluğu')
            ->assertDontSee('Ada Lovelace');
    }

    public function test_a_manager_can_view_but_not_add_contacts(): void
    {
        $this->actingAs(User::factory()->create(['role' => 2]));

        $this->get('/admin/contacts')->assertOk();
        $this->get('/admin/contacts/create')->assertForbidden();
    }

    public function test_an_ordinary_account_cannot_open_the_contacts(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin/contacts')->assertForbidden();
    }

    public function test_a_person_and_an_organization_can_be_added(): void
    {
        $this->actingAs($this->owner());

        $this->post('/admin/contacts', ['type' => 'person', 'first_name' => 'Grace', 'last_name' => 'Hopper', 'identity_number' => '10000000146'])
            ->assertRedirect();
        $this->post('/admin/contacts', ['type' => 'organization', 'organization_name' => 'Özgür Yazılım Derneği', 'identity_number' => '1234567890', 'first_name' => 'yok sayılır'])
            ->assertRedirect();

        $this->assertDatabaseHas('contacts', ['first_name' => 'Grace', 'type' => 'person']);
        $this->assertDatabaseHas('contacts', ['organization_name' => 'Özgür Yazılım Derneği', 'type' => 'organization', 'first_name' => null]);
    }

    public function test_the_required_fields_depend_on_the_record_type(): void
    {
        $this->actingAs($this->owner());

        $this->post('/admin/contacts', ['type' => 'person', 'organization_name' => 'X'])
            ->assertSessionHasErrors(['first_name', 'last_name']);
        $this->post('/admin/contacts', ['type' => 'organization', 'identity_number' => '10000000146'])
            ->assertSessionHasErrors(['organization_name', 'identity_number']);
    }

    public function test_a_contact_with_an_account_is_not_edited_here(): void
    {
        $user = User::factory()->create(['name' => 'Ada']);
        $this->actingAs($this->owner());

        $this->put("/admin/contacts/{$user->contact_id}", ['type' => 'person', 'first_name' => 'Başka', 'last_name' => 'Ad'])
            ->assertSessionHas('danger-status');

        $this->assertSame('Ada', $user->contact->fresh()->first_name);
    }

    public function test_affiliations_are_added_ended_and_kept_as_history(): void
    {
        $contact = Contact::create(['first_name' => 'Ada']);
        $board = AffiliationType::findByKey('board');
        $this->actingAs($this->owner());

        $this->post("/admin/contacts/{$contact->id}/affiliations", ['affiliation_type_id' => $board->id, 'title' => 'Başkan', 'started_at' => '2026-01-01'])
            ->assertSessionHas('success-status');
        $affiliation = $contact->affiliations()->first();
        $this->assertSame('Başkan', $affiliation->title);

        $this->patch("/admin/contacts/{$contact->id}/affiliations/{$affiliation->id}/end")->assertSessionHas('success-status');

        $this->assertFalse($contact->hasAffiliation('board'));
        $this->assertSame(1, $contact->affiliations()->count());
        $this->get("/admin/contacts/{$contact->id}")->assertOk()->assertSee('Sona erdi');
    }

    public function test_an_affiliation_cannot_be_added_twice_while_it_lasts(): void
    {
        $contact = Contact::create(['first_name' => 'Ada']);
        $contact->affiliate('volunteer');
        $volunteer = AffiliationType::findByKey('volunteer');

        $this->actingAs($this->owner())
            ->post("/admin/contacts/{$contact->id}/affiliations", ['affiliation_type_id' => $volunteer->id])
            ->assertSessionHas('danger-status');

        $this->assertSame(1, $contact->affiliations()->count());
    }

    public function test_an_affiliation_that_grants_roles_needs_the_role_permission(): void
    {
        $board = AffiliationType::findByKey('board');
        $board->roles()->attach(Role::findByKey('manager'));
        $contact = Contact::create(['first_name' => 'Ada']);

        $this->actingAs($this->userWith(['admin.access', 'contacts.view', 'contacts.manage']))
            ->post("/admin/contacts/{$contact->id}/affiliations", ['affiliation_type_id' => $board->id])
            ->assertSessionHas('danger-status');

        $this->assertSame(0, $contact->affiliations()->count());
    }

    public function test_an_affiliation_of_another_contact_cannot_be_ended_through_this_one(): void
    {
        $contact = Contact::create(['first_name' => 'Ada']);
        $other = Contact::create(['first_name' => 'Grace']);
        $affiliation = $other->affiliate('volunteer');

        $this->actingAs($this->owner())
            ->patch("/admin/contacts/{$contact->id}/affiliations/{$affiliation->id}/end")
            ->assertNotFound();
    }
}
