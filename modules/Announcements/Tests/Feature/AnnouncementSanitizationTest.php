<?php

namespace Modules\Announcements\Tests\Feature;

use Modules\Announcements\Models\Announcements;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementSanitizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_announcement_html_is_sanitized_before_storage(): void
    {
        $owner = User::factory()->create(['role' => 1]);

        $this->actingAs($owner)->post('/admin/announcements/create', [
            'subject' => 'Duyuru',
            'detail' => '<p>Güvenli</p><script>alert(1)</script><img src=x onerror=alert(1)>',
            'started_at' => now()->format('Y-m-d\\TH:i'),
            'finished_at' => now()->addDay()->format('Y-m-d\\TH:i'),
            'status' => 1,
        ])->assertRedirect(route('admin.announcements'));

        $announcement = Announcements::firstOrFail();
        $this->assertStringContainsString('<p>Güvenli</p>', $announcement->detail);
        $this->assertStringNotContainsString('<script', $announcement->detail);
        $this->assertStringNotContainsString('onerror', $announcement->detail);
    }
}
