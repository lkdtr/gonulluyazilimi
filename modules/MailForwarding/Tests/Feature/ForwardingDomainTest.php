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
}
