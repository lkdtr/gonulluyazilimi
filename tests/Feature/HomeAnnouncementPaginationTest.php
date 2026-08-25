<?php

namespace Tests\Feature;

use App\Models\Announcements;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeAnnouncementPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_announcements_are_paginated_on_home(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 11) as $number) {
            $announcement = new Announcements();
            $announcement->subject = sprintf('Duyuru-%03d', $number);
            $announcement->detail = 'İçerik';
            $announcement->started_at = now()->subDay();
            $announcement->finished_at = now()->addDay();
            $announcement->status = 1;
            $announcement->save();
        }

        $this->actingAs($user)
            ->get('/home')
            ->assertOk()
            ->assertSee('Duyuru-011')
            ->assertDontSee('Duyuru-001')
            ->assertSee('?page=2', false);

        $this->actingAs($user)
            ->get('/home?page=2')
            ->assertOk()
            ->assertSee('Duyuru-001')
            ->assertDontSee('Duyuru-011');
    }
}
