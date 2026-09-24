<?php

namespace Tests\Feature;

use App\Models\AffiliationType;
use App\Models\Contact;
use App\Models\ContactAffiliation;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AffiliationTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_association_roles_are_available_as_types(): void
    {
        $this->assertSame(
            ['volunteer', 'member', 'board', 'audit-board', 'discipline-board'],
            AffiliationType::orderBy('sort')->pluck('key')->all()
        );
        $this->assertTrue(AffiliationType::findByKey('member')->is_system);
        $this->assertTrue(AffiliationType::findByKey('board')->has_term);
    }

    public function test_a_contact_can_hold_several_affiliations_at_once(): void
    {
        $contact = Contact::create(['first_name' => 'Ada', 'last_name' => 'Lovelace']);

        $contact->affiliate('member');
        $contact->affiliate('volunteer');
        $contact->affiliate('board', ['title' => 'Başkan']);

        $this->assertTrue($contact->hasAffiliation('member'));
        $this->assertTrue($contact->hasAffiliation('volunteer'));
        $this->assertTrue($contact->hasAffiliation('board'));
        $this->assertFalse($contact->hasAffiliation('discipline-board'));
        $this->assertSame('Başkan', $contact->affiliations()->ofType('board')->first()->title);
    }

    public function test_affiliating_twice_keeps_a_single_active_affiliation(): void
    {
        $contact = Contact::create(['first_name' => 'Ada']);

        $contact->affiliate('volunteer');
        $contact->affiliate('volunteer');

        $this->assertSame(1, $contact->affiliations()->count());
    }

    public function test_ending_an_affiliation_keeps_it_as_history(): void
    {
        $contact = Contact::create(['first_name' => 'Ada']);
        $contact->affiliate('board');

        $contact->endAffiliation('board');
        $contact->affiliate('board');

        $this->assertTrue($contact->hasAffiliation('board'));
        $this->assertSame(2, $contact->affiliations()->ofType('board')->count());
        $this->assertSame(1, $contact->affiliations()->ofType('board')->active()->count());
    }

    public function test_an_lkd_member_number_makes_the_contact_a_member(): void
    {
        $user = User::factory()->create();
        $this->assertFalse($user->contact->hasAffiliation('member'));

        $user->lkd_user_id = 42;
        $user->save();
        $this->assertTrue($user->contact->hasAffiliation('member'));

        $user->lkd_user_id = null;
        $user->save();
        $this->assertFalse($user->contact->hasAffiliation('member'));
        $this->assertSame(1, $user->contact->affiliations()->ofType('member')->count());
    }

    public function test_registering_makes_the_person_a_volunteer(): void
    {
        $user = User::factory()->create();

        event(new Registered($user));

        $this->assertTrue($user->contact->hasAffiliation('volunteer'));
    }

    public function test_the_backfill_migration_splits_members_and_volunteers(): void
    {
        $member = User::factory()->create(['lkd_user_id' => 7]);
        $volunteer = User::factory()->create();
        $removed = User::factory()->create(['status' => 0]);
        ContactAffiliation::query()->delete();

        $migration = require database_path('migrations/2026_09_23_000003_create_affiliations_for_existing_users.php');
        $migration->up();
        $migration->up();

        $this->assertTrue($member->contact->hasAffiliation('member'));
        $this->assertFalse($member->contact->hasAffiliation('volunteer'));
        $this->assertTrue($volunteer->contact->hasAffiliation('volunteer'));
        $this->assertSame(0, $removed->contact->affiliations()->count());
        $this->assertSame(2, DB::table('contact_affiliations')->count());
    }
}
