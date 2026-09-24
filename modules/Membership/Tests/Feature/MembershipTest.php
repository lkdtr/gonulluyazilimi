<?php

namespace Modules\Membership\Tests\Feature;

use App\Models\Agreement;
use App\Models\AgreementAcceptance;
use App\Models\Contact;
use App\Models\User;
use App\Modules\ContactFields;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Modules\Membership\Models\Membership;
use Modules\Membership\Support\MembershipService;
use Tests\TestCase;

class MembershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_existing_member_numbers_and_member_affiliations_are_carried_over(): void
    {
        $numbered = User::factory()->create(['lkd_user_id' => 506]);
        $unnumbered = Contact::create(['first_name' => 'Kurul', 'last_name' => 'Üyesi']);
        $unnumbered->affiliate('member', ['started_at' => '2021-03-01']);
        User::factory()->create();
        DB::table('membership_events')->delete();
        DB::table('memberships')->delete();

        $migration = require base_path('modules/Membership/database/migrations/2026_09_27_100000_create_membership_tables.php');
        (fn () => $this->backfill())->call($migration);

        $this->assertSame(2, Membership::count());
        $this->assertSame('506', Membership::where('contact_id', $numbered->contact_id)->value('number'));
        $carried = Membership::where('contact_id', $unnumbered->id)->sole();
        $this->assertNull($carried->number);
        $this->assertSame('2021-03-01', $carried->joined_at->toDateString());
        $this->assertSame(['joined'], $carried->events()->pluck('type')->all());
    }

    public function test_managers_make_a_contact_a_member_with_the_next_number(): void
    {
        Membership::create(['contact_id' => Contact::create(['first_name' => 'A', 'last_name' => 'B'])->id, 'number' => '1506']);
        $owner = User::factory()->create(['role' => 1]);
        $contact = Contact::create(['first_name' => 'Ada', 'last_name' => 'Lovelace']);

        $this->assertSame('1507', app(MembershipService::class)->nextNumber());
        $this->actingAs($owner)->get("/admin/contacts/{$contact->id}")->assertOk()->assertSee('Üye yap')->assertSee('1507');

        $this->actingAs($owner)->post("/admin/contacts/{$contact->id}/membership", ['number' => '1506', 'joined_at' => '2026-09-01'])->assertSessionHasErrors('number');
        $this->actingAs($owner)->post("/admin/contacts/{$contact->id}/membership", ['number' => '1507', 'joined_at' => '2026-09-01', 'note' => 'YK 2026/12'])->assertRedirect();

        $membership = Membership::where('contact_id', $contact->id)->sole();
        $this->assertTrue($membership->isActive());
        $this->assertTrue($contact->hasAffiliation('member'));
        $this->assertSame('YK 2026/12', $membership->events()->sole()->note);
        $this->assertSame('1507', app(ContactFields::class)->value('member_number', $contact));
    }

    public function test_status_changes_follow_the_member_affiliation_and_the_history(): void
    {
        $owner = User::factory()->create(['role' => 1]);
        $contact = Contact::create(['first_name' => 'Ada', 'last_name' => 'Lovelace']);
        $membership = app(MembershipService::class)->start($contact, '10', today()->subYear());

        $this->actingAs($owner)->patch("/admin/memberships/{$membership->id}/status", ['status' => 'suspended', 'date' => today()->toDateString(), 'note' => 'Aidat'])->assertRedirect();
        $this->assertFalse($contact->fresh()->hasAffiliation('member'));

        $this->actingAs($owner)->patch("/admin/memberships/{$membership->id}/status", ['status' => 'active', 'date' => today()->toDateString()]);
        $this->assertTrue($contact->fresh()->hasAffiliation('member'));

        $this->actingAs($owner)->patch("/admin/memberships/{$membership->id}/status", ['status' => 'left', 'date' => today()->toDateString()]);
        $membership->refresh();
        $this->assertSame(Membership::LEFT, $membership->status);
        $this->assertSame(today()->toDateString(), $membership->left_at->toDateString());
        $this->assertEqualsCanonicalizing(['joined', 'suspended', 'reactivated', 'left'], $membership->events()->pluck('type')->all());

        $this->actingAs($owner)->put("/admin/memberships/{$membership->id}", ['number' => '11', 'derbis_registered' => '1'])->assertRedirect();
        $this->assertSame('11', $membership->fresh()->number);
        $this->assertTrue($membership->fresh()->derbis_registered);
        $this->assertTrue($membership->events()->where('type', 'number_changed')->where('note', '10 → 11')->exists());
    }

    public function test_the_member_sees_membership_history_and_agreements_on_the_profile(): void
    {
        $user = User::factory()->create();
        app(MembershipService::class)->start($user->contact, '42', today());
        $agreement = Agreement::create(['key' => 'kvkk', 'title' => 'Gizlilik Politikası']);
        $version = $agreement->versions()->create(['version' => 1, 'content' => '<p>x</p>']);
        $version->forceFill(['published_at' => now()])->save();
        AgreementAcceptance::create(['agreement_version_id' => $version->id, 'user_id' => $user->id, 'contact_id' => $user->contact_id, 'ip' => '10.1.2.3', 'accepted_at' => now()]);

        $this->actingAs($user)->get('/my-infos')->assertOk()
            ->assertSee('Üyelik tarihçesi')->assertSee('Üyelik başladı')->assertSee('42')
            ->assertSee('Sözleşmelerim')->assertSee('Gizlilik Politikası')->assertSee('10.1.2.3')
            ->assertDontSee('name="lkd_user_id"', false);
    }

    public function test_the_member_list_needs_the_permission(): void
    {
        $contact = Contact::create(['first_name' => 'Ada', 'last_name' => 'Lovelace']);
        app(MembershipService::class)->start($contact, '7', today());

        $this->actingAs(User::factory()->create(['role' => 1]))->get('/admin/memberships')->assertOk()->assertSee('Ada Lovelace');
        $this->actingAs(User::factory()->create(['role' => 2]))->get('/admin/memberships')->assertForbidden();
    }
}
