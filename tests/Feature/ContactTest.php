<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_new_account_gets_a_contact_mirroring_its_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Ada',
            'surname' => 'Lovelace',
            'national_id' => '10000000146',
            'email' => 'ada@example.org',
            'phone_number' => '+905551112233',
            'birthday' => '1990-12-10',
            'city_id' => 34,
        ]);

        $contact = $user->fresh()->contact;

        $this->assertNotNull($contact);
        $this->assertSame(Contact::TYPE_PERSON, $contact->type);
        $this->assertSame('Ada Lovelace', $contact->display_name);
        $this->assertSame('10000000146', $contact->identity_number);
        $this->assertSame('ada@example.org', $contact->email);
        $this->assertSame('+905551112233', $contact->phone);
        $this->assertSame('1990-12-10', $contact->birthday->toDateString());
        $this->assertSame(34, $contact->city_id);
        $this->assertTrue($contact->user->is($user));
    }

    public function test_updating_an_account_updates_the_same_contact(): void
    {
        $user = User::factory()->create(['email' => 'old@example.org']);
        $contactId = $user->contact_id;

        $user->update(['email' => 'new@example.org', 'phone_number' => '+905550000000']);

        $this->assertSame(1, Contact::count());
        $contact = Contact::find($contactId);
        $this->assertSame('new@example.org', $contact->email);
        $this->assertSame('+905550000000', $contact->phone);
    }

    public function test_an_unset_city_is_stored_as_no_city(): void
    {
        $user = User::factory()->create(['city_id' => 0]);

        $this->assertNull($user->contact->city_id);
    }

    public function test_the_backfill_migration_links_existing_accounts_once(): void
    {
        DB::table('users')->insert([
            'name' => 'Grace',
            'surname' => 'Hopper',
            'email' => 'grace@example.org',
            'password' => 'x',
            'city_id' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $migration = require database_path('migrations/2026_09_23_000001_create_contacts_for_existing_users.php');
        $migration->up();
        $migration->up();

        $user = User::where('email', 'grace@example.org')->first();
        $this->assertSame(1, Contact::count());
        $this->assertSame('Grace Hopper', $user->contact->display_name);
        $this->assertNull($user->contact->city_id);
    }

    public function test_an_organization_is_named_by_its_organization_name(): void
    {
        $contact = Contact::create([
            'type' => Contact::TYPE_ORGANIZATION,
            'organization_name' => 'Linux Kullanıcıları Derneği',
            'identity_number' => '1234567890',
        ]);

        $this->assertTrue($contact->isOrganization());
        $this->assertSame('Linux Kullanıcıları Derneği', $contact->display_name);
        $this->assertNull($contact->user);
    }
}
