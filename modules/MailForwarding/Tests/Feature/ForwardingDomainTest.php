<?php

namespace Modules\MailForwarding\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForwardingDomainTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_alias_must_use_the_forwarding_domain(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->from('/email-forwarding')->post('/email-forwarding', [
            'email_alias' => 'invalid@example.test',
            'agreement' => '1',
        ])->assertRedirect('/email-forwarding')
            ->assertSessionHasErrors('email_alias');
    }

    public function test_the_dashboard_names_the_forwarding_domain(): void
    {
        $this->actingAs(User::factory()->create(['role' => 1]))->get('/admin')
            ->assertOk()
            ->assertSee('@'.config('mail-forwarding.domain').' adresi')
            ->assertDontSee('@linux.org.tr adresi');
    }
}
