<?php

namespace Tests\Feature;

use App\Mail\AccountActivation as ActivationMail;
use App\Models\Contact;
use App\Models\User;
use App\Support\AccountActivation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AccountActivationTest extends TestCase
{
    use RefreshDatabase;

    private function importedMember(): Contact
    {
        $contact = Contact::create(['first_name' => 'Ada', 'last_name' => 'Lovelace', 'email' => 'ada@example.org', 'phone' => '905551112233']);
        $contact->affiliate('member', ['started_at' => '2020-01-01']);

        return $contact;
    }

    public function test_the_request_form_sends_a_link_only_to_an_eligible_contact_without_revealing_it(): void
    {
        Mail::fake();
        $this->importedMember();
        User::factory()->create(['email' => 'has-account@example.org']);

        $this->post('/activate', ['email' => 'ada@example.org'])->assertSessionHas('status');
        $this->post('/activate', ['email' => 'nobody@example.org'])->assertSessionHas('status');
        $this->post('/activate', ['email' => 'has-account@example.org'])->assertSessionHas('status');

        Mail::assertQueued(ActivationMail::class, 1);
        Mail::assertQueued(ActivationMail::class, fn ($mail) => $mail->hasTo('ada@example.org') && str_contains($mail->link, '/activate/'));
    }

    public function test_the_link_creates_an_account_on_the_existing_contact_and_keeps_its_membership(): void
    {
        $contact = $this->importedMember();
        $link = app(AccountActivation::class)->link($contact);

        $this->get($link)->assertOk()->assertSee('Parolanızı belirleyin')->assertSee('ada@example.org');
        $this->post($link, ['password' => 'short', 'password_confirmation' => 'short'])->assertSessionHasErrors('password');
        $this->post($link, ['password' => 'password123', 'password_confirmation' => 'password123'])->assertRedirect('/home');

        $user = User::where('email', 'ada@example.org')->sole();
        $this->assertAuthenticatedAs($user);
        $this->assertSame($contact->id, $user->contact_id);
        $this->assertSame(1, Contact::count());
        $this->assertTrue($contact->fresh()->hasAffiliation('member'), 'Activation must not end the membership.');
        $this->assertFalse($contact->fresh()->hasAffiliation('volunteer'), 'Activation does not make an imported member a volunteer.');

        // Used once: the contact now has an account.
        auth()->logout();
        $this->get($link)->assertRedirect('/login');
    }

    public function test_tampered_or_expired_links_are_refused(): void
    {
        $contact = $this->importedMember();
        $link = app(AccountActivation::class)->link($contact);

        $this->get($link.'x')->assertForbidden();
        $this->travel(AccountActivation::LINK_HOURS + 1)->hours();
        $this->get($link)->assertForbidden();
    }

    public function test_the_link_stops_working_when_the_email_changes(): void
    {
        $contact = $this->importedMember();
        $link = app(AccountActivation::class)->link($contact);
        $contact->update(['email' => 'new@example.org']);

        $this->get($link)->assertRedirect('/login');
        $this->post($link, ['password' => 'password123', 'password_confirmation' => 'password123'])->assertForbidden();
    }

    public function test_managers_send_the_link_from_the_contact_page(): void
    {
        Mail::fake();
        $contact = $this->importedMember();
        $owner = User::factory()->create(['role' => 1]);

        $this->actingAs($owner)->get("/admin/contacts/{$contact->id}")->assertSee('Etkinleştirme bağlantısı gönder');
        $this->actingAs($owner)->post("/admin/contacts/{$contact->id}/activation")->assertSessionHas('success-status');

        Mail::assertQueued(ActivationMail::class, fn ($mail) => $mail->hasTo('ada@example.org'));
    }
}
