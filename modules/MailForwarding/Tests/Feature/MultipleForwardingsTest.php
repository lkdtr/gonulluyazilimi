<?php

namespace Modules\MailForwarding\Tests\Feature;

use App\Models\User;
use App\Modules\ContactFields;
use App\Support\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\MailForwarding\Models\EmailRedirects;
use Modules\MailForwarding\Support\ForwardingPolicy;
use Tests\TestCase;

class MultipleForwardingsTest extends TestCase
{
    use RefreshDatabase;

    private function volunteerAndMember(): User
    {
        app(Organization::class)->save(['mail_forwarding_domains' => json_encode(['volunteer' => 'penguen.org.tr', 'member' => 'linux.org.tr'])]);
        $user = User::factory()->create(['name' => 'Ada', 'surname' => 'Lovelace', 'email' => 'ada@example.org']);
        $user->contact->affiliate('volunteer');
        $user->contact->affiliate('member');

        return $user;
    }

    public function test_a_volunteer_member_may_hold_an_address_on_each_domain(): void
    {
        $user = $this->volunteerAndMember();

        $this->assertSame(['penguen.org.tr' => 'Gönüllü', 'linux.org.tr' => 'Üye'], app(ForwardingPolicy::class)->domainsFor($user));

        $this->actingAs($user)->get('/email-redirects')->assertOk()->assertSee('@penguen.org.tr')->assertSee('@linux.org.tr')->assertSee('value="penguen.org.tr"', false);
        $this->actingAs($user)->get('/email-redirects?domain=linux.org.tr')->assertOk()->assertSee('value="linux.org.tr"', false);
        // A domain the person is not eligible for falls back to their first one.
        $this->actingAs($user)->get('/email-redirects?domain=example.com')->assertOk()->assertSee('value="penguen.org.tr"', false);
    }

    public function test_both_addresses_forward_to_the_same_personal_email(): void
    {
        $user = $this->volunteerAndMember();

        EmailRedirects::create(['user_id' => $user->id, 'email_alias' => 'ada.lovelace@penguen.org.tr', 'email_forwarding' => 'ada@example.org', 'status' => 1]);
        EmailRedirects::create(['user_id' => $user->id, 'email_alias' => 'ada.lovelace@linux.org.tr', 'email_forwarding' => 'ada@example.org', 'status' => 1]);

        $this->assertSame(['linux.org.tr', 'penguen.org.tr'], EmailRedirects::where('user_id', $user->id)->orderBy('domain')->pluck('domain')->all());

        $fields = app(ContactFields::class);
        $this->assertSame('ada.lovelace@penguen.org.tr', $fields->value('forwarding_email.penguen.org.tr', $user->contact));
        $this->assertSame('ada.lovelace@linux.org.tr', $fields->value('forwarding_email.linux.org.tr', $user->contact));
        $this->assertSame('ada.lovelace@penguen.org.tr, ada.lovelace@linux.org.tr', $fields->value('forwarding_email', $user->contact));

        $this->expectException(\Illuminate\Database\QueryException::class);
        EmailRedirects::create(['user_id' => $user->id, 'email_alias' => 'ada.l@penguen.org.tr', 'email_forwarding' => 'ada@example.org', 'status' => 0]);
    }

    public function test_the_domain_follows_the_alias(): void
    {
        $user = User::factory()->create();
        $redirect = EmailRedirects::create(['user_id' => $user->id, 'email_alias' => 'x.y@Penguen.org.tr', 'email_forwarding' => 'x@example.org', 'status' => 1]);
        $this->assertSame('penguen.org.tr', $redirect->domain);

        $redirect->update(['email_alias' => 'x.y@linux.org.tr']);
        $this->assertSame('linux.org.tr', $redirect->fresh()->domain);
    }
}
