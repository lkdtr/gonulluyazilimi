<?php

namespace Tests\Feature;

use App\Models\Announcements;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentAndForwardingSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_announcement_html_is_sanitized_before_storage(): void
    {
        $owner = User::factory()->create(['role' => 1]);

        $this->actingAs($owner)->post('/new-announcement', [
            'subject' => 'Duyuru',
            'detail' => '<p>Güvenli</p><script>alert(1)</script><img src=x onerror=alert(1)>',
            'started_at' => now()->format('Y-m-d\\TH:i'),
            'finished_at' => now()->addDay()->format('Y-m-d\\TH:i'),
            'status' => 1,
        ])->assertRedirect(secure_url('/announcements'));

        $announcement = Announcements::firstOrFail();
        $this->assertStringContainsString('<p>Güvenli</p>', $announcement->detail);
        $this->assertStringNotContainsString('<script', $announcement->detail);
        $this->assertStringNotContainsString('onerror', $announcement->detail);
    }

    public function test_email_alias_must_be_a_penguen_domain_address(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->from('/email-forwarding')->post('/email-forwarding', [
            'email_alias' => 'invalid@example.test',
            'agreement' => '1',
        ])->assertRedirect('/email-forwarding')
            ->assertSessionHasErrors('email_alias');
    }
}
