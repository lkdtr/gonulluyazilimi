<?php

namespace Tests\Feature;

use App\Models\Agreement;
use App\Models\AgreementAcceptance;
use App\Models\ContactPermissions;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\LkdAgreementSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AgreementTest extends TestCase
{
    use RefreshDatabase;

    private function agreementManager(): User
    {
        $role = Role::create(['key' => 'agreement-manager', 'name' => 'Sözleşme sorumlusu']);
        $role->syncPermissions(['admin.access', 'agreements.manage']);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        return $user;
    }

    private function publish(string $key, string $content, string $title = 'Gizlilik Politikası'): Agreement
    {
        $agreement = Agreement::firstOrCreate(['key' => $key], ['title' => $title]);
        $version = $agreement->versions()->create(['version' => (int) $agreement->versions()->max('version') + 1, 'content' => $content]);
        $version->forceFill(['published_at' => now()])->save();

        return $agreement;
    }

    private function register(array $overrides = []): \Illuminate\Testing\TestResponse
    {
        Mail::fake();
        ContactPermissions::create(['value_type' => 'phone_number', 'value' => '905551112233', 'verified' => true, 'verified_at' => now(), 'status' => 1]);

        return $this->post('/register', $overrides + [
            'name' => 'Ayşe', 'surname' => 'Yılmaz', 'email' => 'ayse@example.test', 'phone_number' => '905551112233',
            'password' => 'password123', 'password_confirmation' => 'password123',
        ]);
    }

    public function test_without_a_published_agreement_there_is_nothing_to_accept(): void
    {
        $this->get('/register')->assertOk()->assertDontSee('koşullarını kabul ediyorum');

        $this->register()->assertRedirect('/home');
        $this->assertSame(0, AgreementAcceptance::count());
    }

    public function test_registration_requires_and_records_the_privacy_policy_in_force(): void
    {
        $agreement = $this->publish(Agreement::PRIVACY, '<p>Metin</p>');

        $this->get('/register')->assertOk()->assertSee('Gizlilik Politikası')->assertSee('/agreements/kvkk', false);
        $this->register()->assertSessionHasErrors('agreement');

        $this->register(['agreement' => 'true'])->assertRedirect('/home');

        $acceptance = AgreementAcceptance::sole();
        $this->assertTrue($acceptance->version->is($agreement->currentVersion));
        $this->assertSame('register', $acceptance->context);
        $this->assertSame('127.0.0.1', $acceptance->ip);
        $this->assertSame(User::where('email', 'ayse@example.test')->value('id'), $acceptance->user_id);
    }

    public function test_drafts_do_not_change_the_version_in_force_until_published(): void
    {
        $agreement = $this->publish(Agreement::PRIVACY, '<p>Birinci metin</p>');
        $manager = $this->agreementManager();

        $this->actingAs($manager)->put("/admin/agreements/{$agreement->id}", [
            'title' => 'Gizlilik Politikası', 'content' => '<p>İkinci metin</p><script>x()</script>', 'action' => 'draft',
        ])->assertRedirect("/admin/agreements/{$agreement->id}/edit");

        $this->get('/agreements/kvkk')->assertSee('Birinci metin')->assertDontSee('İkinci metin');
        $this->assertSame('<p>İkinci metin</p>', $agreement->draft()->first()->content);

        $this->actingAs($manager)->put("/admin/agreements/{$agreement->id}", [
            'title' => 'Gizlilik Politikası', 'content' => '<p>İkinci metin</p>', 'action' => 'publish',
        ]);

        $agreement->refresh();
        $this->assertSame(2, $agreement->currentVersion->version);
        $this->assertNull($agreement->draft()->first());
        $this->get('/agreements/kvkk')->assertSee('İkinci metin')->assertSee('Sürüm 2');
        $this->get('/agreements/kvkk?version=1')->assertSee('Birinci metin')->assertSee('Eski sürüm');
        // The address used before agreements were editable still works.
        $this->get('/user-agreement?iframe')->assertOk()->assertSee('İkinci metin');
    }

    public function test_managers_create_agreements_and_see_acceptances(): void
    {
        $manager = $this->agreementManager();

        $this->actingAs($manager)->post('/admin/agreements', [
            'key' => 'aidat', 'title' => 'Aidat Sözleşmesi', 'content' => '<p>Aidat</p>', 'action' => 'publish',
        ])->assertRedirect();

        $agreement = Agreement::findByKey('aidat');
        $this->assertSame(1, $agreement->currentVersion->version);

        $user = User::factory()->create(['name' => 'Grace', 'surname' => 'Hopper']);
        AgreementAcceptance::create(['agreement_version_id' => $agreement->currentVersion->id, 'user_id' => $user->id, 'contact_id' => $user->contact_id, 'context' => 'register', 'ip' => '10.0.0.1', 'accepted_at' => now()]);

        $this->actingAs($manager)->get('/admin/agreements')->assertOk()->assertSee('Aidat Sözleşmesi');
        $this->actingAs($manager)->get("/admin/agreements/{$agreement->id}")->assertOk()->assertSee('Grace Hopper')->assertSee('10.0.0.1');
        $this->actingAs(User::factory()->create(['role' => 2]))->get('/admin/agreements')->assertForbidden();
    }

    public function test_the_lkd_seeder_publishes_the_former_texts_and_records_past_acceptances(): void
    {
        $user = User::factory()->create(['agreement_at' => '2024-05-01 10:00:00']);
        User::factory()->create(['agreement_at' => null]);

        $this->seed(LkdAgreementSeeder::class);
        $this->seed(LkdAgreementSeeder::class);

        $this->assertSame(1, Agreement::findByKey(Agreement::PRIVACY)->versions()->count());
        $this->assertNotNull(Agreement::findByKey(Agreement::EMAIL_USAGE)->currentVersion);
        $this->get('/agreements/kvkk')->assertOk()->assertSee('6698 sayılı');

        $acceptance = AgreementAcceptance::sole();
        $this->assertSame($user->id, $acceptance->user_id);
        $this->assertSame('2024-05-01 10:00:00', $acceptance->accepted_at->format('Y-m-d H:i:s'));
    }
}
