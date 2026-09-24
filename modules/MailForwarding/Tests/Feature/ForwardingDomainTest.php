<?php

namespace Modules\MailForwarding\Tests\Feature;

use App\Models\User;
use App\Support\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\MailForwarding\Support\ForwardingPolicy;
use Tests\TestCase;

class ForwardingDomainTest extends TestCase
{
    use RefreshDatabase;

    private function volunteer(): User
    {
        $user = User::factory()->create();
        $user->contact->affiliate('volunteer');

        return $user;
    }

    public function test_email_alias_must_use_the_forwarding_domain(): void
    {
        $this->actingAs($this->volunteer())->from('/email-forwarding')->post('/email-forwarding', [
            'email_alias' => 'invalid@example.test',
            'agreement' => '1',
        ])->assertRedirect('/email-forwarding')
            ->assertSessionHasErrors('email_alias');
    }

    public function test_the_dashboard_names_the_forwarding_domain(): void
    {
        $this->actingAs(User::factory()->create(['role' => 1]))->get('/admin')
            ->assertOk()
            ->assertSee('@'.config('mail-forwarding.domain'));
    }

    public function test_by_default_only_volunteers_get_an_address(): void
    {
        $member = User::factory()->create();
        $member->contact->affiliate('member');

        $this->actingAs($member)->get('/email-redirects')->assertRedirect('/home');
        $this->actingAs($member)->get('/home')->assertDontSee('/email-redirects', false);

        $volunteer = $this->volunteer();
        $this->actingAs($volunteer)->get('/email-redirects')->assertOk();
        $this->assertSame(config('mail-forwarding.domain'), app(ForwardingPolicy::class)->domainFor($volunteer));
    }

    public function test_the_association_chooses_who_gets_an_address_and_on_which_domain(): void
    {
        $owner = User::factory()->create(['role' => 1]);
        $this->actingAs($owner)->get('/admin/forwarding/settings')->assertOk()->assertSee('Gönüllü')->assertSee('Üye');

        $this->actingAs($owner)->put('/admin/forwarding/settings', [
            'domains' => ['volunteer' => '', 'member' => 'Uyeler.Example.org'],
            'label' => 'Üye e-posta adresi',
        ])->assertRedirect();

        $policy = app(ForwardingPolicy::class);
        $this->assertSame(['member' => 'uyeler.example.org'], $policy->domains());
        $this->assertSame('Üye e-posta adresi', $policy->label());

        $member = User::factory()->create();
        $member->contact->affiliate('member');
        $this->assertSame('uyeler.example.org', $policy->domainFor($member));
        $this->assertNull($policy->domainFor($this->volunteer()));

        $this->actingAs($owner)->put('/admin/forwarding/settings', ['domains' => ['member' => 'not a domain'], 'label' => 'X'])->assertSessionHasErrors('domains.member');
    }

    public function test_no_domain_means_nobody_gets_an_address(): void
    {
        app(Organization::class)->save(['mail_forwarding_domains' => json_encode([])]);

        $this->actingAs($this->volunteer())->get('/email-redirects')->assertRedirect('/home');
    }
}
