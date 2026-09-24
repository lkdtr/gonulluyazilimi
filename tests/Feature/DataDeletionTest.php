<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\ContactPhoto;
use App\Models\DataDeletionRequest;
use App\Models\ProcessLogs;
use App\Models\User;
use App\Support\Consents;
use App\Support\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Modules\IdCard\Models\IdCard;
use Modules\IdCard\Support\CardIssuer;
use Modules\MailForwarding\Models\EmailRedirects;
use Modules\Reference\Models\ReferenceRequests;
use Tests\Concerns\MakesImages;
use Tests\TestCase;

class DataDeletionTest extends TestCase
{
    use MakesImages, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    private function sentTo(string $address): array
    {
        return collect(app('mailer')->getSymfonyTransport()->messages())
            ->filter(fn ($sent) => collect($sent->getOriginalMessage()->getTo())->contains(fn ($to) => $to->getAddress() === $address))
            ->map(fn ($sent) => $sent->getOriginalMessage()->getTextBody())
            ->values()->all();
    }

    private function member(): User
    {
        $user = User::factory()->create(['name' => 'Ada', 'surname' => 'Lovelace', 'email' => 'ada@example.org', 'phone_number' => '905551112233', 'national_id' => '10000000146']);
        $user->contact->affiliate('volunteer');

        return $user;
    }

    public function test_a_person_requests_deletion_with_their_password_and_can_cancel(): void
    {
        app(Organization::class)->save(['notification_email' => 'yk@ornek.org.tr']);
        $user = $this->member();

        $this->actingAs($user)->get('/my-infos')->assertOk()->assertSee('Kişisel verilerimin silinmesi');
        $this->actingAs($user)->post('/my-data-deletion', ['password' => 'wrong'])->assertSessionHasErrors('password', null, 'deletion');
        $this->assertSame(0, DataDeletionRequest::count());

        $this->actingAs($user)->post('/my-data-deletion', ['password' => 'password', 'reason' => 'Artık üye değilim'])->assertRedirect('/my-infos#deletion');
        $this->assertTrue(DataDeletionRequest::sole()->isPending());
        $this->assertCount(1, $this->sentTo('yk@ornek.org.tr'));
        $this->actingAs($user)->get('/my-infos')->assertSee('değerlendirmede');

        $this->actingAs($user)->delete('/my-data-deletion')->assertRedirect('/my-infos#deletion');
        $this->assertSame(DataDeletionRequest::CANCELLED, DataDeletionRequest::sole()->status);
    }

    public function test_approval_removes_the_personal_data_everywhere(): void
    {
        $owner = User::factory()->create(['role' => 1]);
        $user = $this->member();
        $contact = $user->contact;
        $contact->update(['last_name' => 'Byron']);
        app(Consents::class)->set($contact, ['email' => true], 'profile');
        $photo = $contact->photos()->create(['path' => $this->fakePng('me.png', 300, 400)->store(ContactPhoto::DIRECTORY, 'local')]);
        $photo->approve($owner);
        app(CardIssuer::class)->cardsFor($contact);
        EmailRedirects::create(['user_id' => $user->id, 'email_alias' => 'ada.lovelace@penguen.org.tr', 'email_forwarding' => 'ada@example.org', 'status' => 1]);
        (new ReferenceRequests())->forceFill(['user_id' => $user->id])->save();
        $deletion = DataDeletionRequest::create(['contact_id' => $contact->id, 'user_id' => $user->id, 'reason' => 'Gizlilik']);

        $this->actingAs($owner)->get('/admin/data-deletions')->assertOk()->assertSee('ada.lovelace@penguen.org.tr')->assertSee('PostfixAdmin');
        $this->actingAs($owner)->patch("/admin/data-deletions/{$deletion->id}/approve")->assertRedirect();

        $contact = Contact::withTrashed()->find($contact->id);
        $user->refresh();
        $this->assertTrue($contact->trashed());
        $this->assertSame('Silinmiş Kişi', $contact->display_name);
        $this->assertNull($contact->email);
        $this->assertNull($contact->identity_number);
        $this->assertSame('Silinmiş', $user->name);
        $this->assertNull($user->phone_number);
        $this->assertNull($user->national_id);
        $this->assertSame(0, (int) $user->status);
        $this->assertNull(User::where('email', 'ada@example.org')->first());

        Storage::disk('local')->assertMissing($photo->path);
        $this->assertSame(0, $contact->affiliations()->active()->count());
        $this->assertFalse(app(Consents::class)->allows($contact, 'email'));
        $this->assertNotNull(IdCard::where('contact_id', $contact->id)->first()->revoked_at);
        $this->assertSame(0, (int) EmailRedirects::where('user_id', $user->id)->value('status'));
        $this->assertNull(EmailRedirects::where('email_forwarding', 'ada@example.org')->first());
        $this->assertSame(0, ReferenceRequests::where('user_id', $user->id)->count());

        $deletion->refresh();
        $this->assertSame(DataDeletionRequest::COMPLETED, $deletion->status);
        $this->assertNull($deletion->reason);

        // Neither the audit trail nor the free-text log keeps the old values.
        $logs = ProcessLogs::all()->map(fn ($log) => $log->process.json_encode($log->changes, JSON_UNESCAPED_UNICODE))->implode("\n");
        $this->assertStringNotContainsString('Lovelace', $logs);
        $this->assertStringNotContainsString('ada@example.org', $logs);
        $this->assertStringContainsString('Kişisel veriler silindi (veri silme talebi #'.$deletion->id.')', $logs);

        $this->assertCount(1, $this->sentTo('ada@example.org'));
    }

    public function test_rejection_keeps_the_data_and_tells_the_person(): void
    {
        $owner = User::factory()->create(['role' => 1]);
        $user = $this->member();
        $deletion = DataDeletionRequest::create(['contact_id' => $user->contact_id, 'user_id' => $user->id]);

        $this->actingAs($owner)->patch("/admin/data-deletions/{$deletion->id}/reject", ['response' => 'Açık aidat borcu var'])->assertRedirect();

        $this->assertSame(DataDeletionRequest::REJECTED, $deletion->fresh()->status);
        $this->assertSame('Ada', $user->fresh()->name);
        $this->assertStringContainsString('Açık aidat borcu var', $this->sentTo('ada@example.org')[0]);
        $this->actingAs(User::factory()->create(['role' => 2]))->get('/admin/data-deletions')->assertForbidden();
    }
}
