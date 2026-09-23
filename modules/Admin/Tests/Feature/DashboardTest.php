<?php

namespace Modules\Admin\Tests\Feature;

use App\Models\User;
use App\Modules\Dashboard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Modules\Reference\Models\ReferenceRequests;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private function figure(array $stats, string $label): ?int
    {
        foreach ($stats as $stat) {
            if ($stat['label'] === $label) {
                return $stat['value'];
            }
        }

        return null;
    }

    public function test_owner_sees_figures_and_charts_instead_of_menu_cards(): void
    {
        $owner = User::factory()->create(['role' => 1]);
        User::factory()->count(2)->create();
        User::factory()->create(['status' => 0, 'lkd_user_id' => 7]);
        User::factory()->create(['lkd_user_id' => 42]);

        $waiting = new ReferenceRequests();
        $waiting->user_id = $owner->id;
        $waiting->save();

        DB::table('seminar_subjects')->insert([
            ['subject' => 'Linux\'a giriş', 'status' => 1],
            ['subject' => 'Eski konu', 'status' => 0],
        ]);

        $response = $this->actingAs($owner)->get('/admin')->assertOk()
            ->assertSee('Aylık yeni gönüllü')
            ->assertSee('<svg', false);

        $stats = $response->viewData('stats');
        $this->assertSame(3, $this->figure($stats, 'Gönüllü'));
        $this->assertSame(1, $this->figure($stats, 'Üye'));
        $this->assertSame(3, $this->figure($stats, 'Son 30 günde katılan'));
        $this->assertSame(1, $this->figure($stats, 'Referans bekleyen üye'));
        $this->assertSame(1, $this->figure($stats, 'Verilebilir seminer'));

        $charts = collect($response->viewData('charts'))->keyBy('title');
        $this->assertSame(3, last($charts['Toplam gönüllü']['data']));
        $this->assertSame(3, array_sum($charts['Aylık yeni gönüllü']['data']));
    }

    public function test_managers_do_not_see_owner_only_figures(): void
    {
        $manager = User::factory()->create(['role' => 2]);

        $stats = $this->actingAs($manager)->get('/admin')->assertOk()->viewData('stats');

        $this->assertNotNull($this->figure($stats, 'Gönüllü'));
        $this->assertNull($this->figure($stats, 'Referans bekleyen üye'));
        $this->assertNull($this->figure($stats, 'Bekleyen seminer talebi'));
    }

    public function test_monthly_series_counts_and_accumulates_by_month(): void
    {
        $this->travelTo(now()->setDate(2026, 9, 15));

        User::factory()->create(['created_at' => '2025-01-10']);
        User::factory()->create(['created_at' => '2026-07-03']);
        User::factory()->count(2)->create(['created_at' => '2026-09-01']);

        $monthly = Dashboard::monthly(User::query(), 3);
        $this->assertSame([0 => 1, 1 => 0, 2 => 2], array_values($monthly));
        $this->assertCount(3, $monthly);

        $total = Dashboard::monthly(User::query(), 3, cumulative: true);
        $this->assertSame([2, 2, 4], array_values($total));
    }
}
