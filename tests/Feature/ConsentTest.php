<?php

namespace Tests\Feature;

use App\Models\ConsentEvent;
use App\Models\PhoneVerification;
use App\Models\User;
use App\Support\Consents;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ConsentTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_person_changes_their_consents_on_the_profile_page_with_history(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/my-infos')->assertOk()->assertSee('İletişim izinleri');

        $this->actingAs($user)->put('/my-consents', ['consents' => ['email' => '1', 'sms' => '1']])->assertRedirect('/my-infos#consents');
        $this->actingAs($user)->put('/my-consents', ['consents' => ['email' => '1']]);

        $consents = app(Consents::class);
        $this->assertSame(['email' => true, 'sms' => false, 'whatsapp' => false, 'phone' => false], $consents->current($user->contact));
        $this->assertTrue($consents->allows($user->contact, 'email'));
        $this->assertFalse($consents->allows($user->contact, 'sms'));

        // First save: 4 channels asked; second save: only sms changed.
        $this->assertSame(5, ConsentEvent::where('contact_id', $user->contact_id)->count());
        $withdrawal = ConsentEvent::where('channel', 'sms')->latest('id')->first();
        $this->assertFalse($withdrawal->granted);
        $this->assertSame('profile', $withdrawal->source);
        $this->assertSame('127.0.0.1', $withdrawal->ip);
    }

    public function test_never_asked_is_not_a_consent(): void
    {
        $user = User::factory()->create();

        $this->assertSame(['email' => null, 'sms' => null, 'whatsapp' => null, 'phone' => null], app(Consents::class)->current($user->contact));
        $this->assertFalse(app(Consents::class)->allows($user->contact, 'email'));
    }

    public function test_registration_records_the_consents_asked(): void
    {
        Mail::fake();
        PhoneVerification::create(['value_type' => 'phone_number', 'value' => '905551112233', 'verified' => true, 'verified_at' => now(), 'status' => 1]);

        $this->post('/register', [
            'name' => 'Ayşe', 'surname' => 'Yılmaz', 'email' => 'ayse@example.test', 'phone_number' => '905551112233',
            'password' => 'password123', 'password_confirmation' => 'password123', 'consents' => ['whatsapp' => '1'],
        ])->assertRedirect('/home');

        $contact = User::where('email', 'ayse@example.test')->firstOrFail()->contact;
        $this->assertSame(['email' => false, 'sms' => false, 'whatsapp' => true, 'phone' => null], app(Consents::class)->current($contact));
        $this->assertSame(['register'], ConsentEvent::where('contact_id', $contact->id)->distinct()->pluck('source')->all());
    }

    public function test_managers_record_a_change_the_person_reported(): void
    {
        $owner = User::factory()->create(['role' => 1]);
        $member = User::factory()->create();

        $this->actingAs($owner)->put("/admin/contacts/{$member->contact_id}/consents", ['consents' => ['phone' => '1']])->assertRedirect();

        $event = ConsentEvent::where('contact_id', $member->contact_id)->where('channel', 'phone')->sole();
        $this->assertTrue($event->granted);
        $this->assertSame('admin', $event->source);
        $this->assertSame($owner->id, $event->user_id);
        $this->actingAs($owner)->get("/admin/contacts/{$member->contact_id}")->assertOk()->assertSee('Geri alındı')->assertSee('Yönetici');
        $this->actingAs(User::factory()->create(['role' => 2]))->put("/admin/contacts/{$member->contact_id}/consents", ['consents' => []])->assertForbidden();
    }
}
