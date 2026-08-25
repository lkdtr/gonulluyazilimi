<?php

namespace Tests\Feature;

use App\Mail\SeminarOfferNotification;
use App\Mail\SeminarOfferReceived;
use App\Models\SeminarSubjects;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SeminarOfferTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_fill_the_form_but_must_log_in_before_submitting(): void
    {
        $subject = $this->subject();

        $this->post('/create-seminar-offer', $this->data(['seminar_subject_id' => $subject->id]))
            ->assertRedirect('/login');

        $this->assertSame('Örnek özgeçmiş', session('seminar_offer_form.biography'));
        $this->assertDatabaseCount('seminar_offers', 0);
    }

    public function test_offer_form_uses_the_iframe_layout_when_requested(): void
    {
        $this->get('/create-seminar-offer?in-iframe=1')
            ->assertOk()
            ->assertDontSee('navbar')
            ->assertHeader('Content-Security-Policy', "frame-ancestors 'self' https://lkd.org.tr https://www.lkd.org.tr");
    }

    public function test_authenticated_member_submission_notifies_member_and_board(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $subject = $this->subject();

        $this->actingAs($user)->post('/create-seminar-offer', $this->data(['seminar_subject_id' => $subject->id]))
            ->assertSessionHas('success-status');

        $this->assertDatabaseHas('seminar_offers', ['user_id' => $user->id, 'seminar_subject_id' => $subject->id]);
        Mail::assertSent(SeminarOfferNotification::class, fn ($mail) => $mail->hasTo('yk@lkd.org.tr'));
        Mail::assertSent(SeminarOfferReceived::class, fn ($mail) => $mail->hasTo($user->email));
    }

    public function test_owner_can_accept_a_proposed_subject_into_the_subject_pool(): void
    {
        $owner = User::factory()->create(['role' => 1]);
        $applicant = User::factory()->create();
        $this->actingAs($applicant)->post('/create-seminar-offer', $this->data([
            'subject_choice' => 'proposed', 'seminar_subject_id' => null, 'proposed_subject' => 'Konteyner Güvenliği',
        ]));
        $proposal = \App\Models\SeminarSubjectProposals::firstOrFail();

        $this->actingAs($owner)->patch(route('admin.seminar-subject-proposals.accept', $proposal))
            ->assertSessionHas('success-status');

        $this->assertDatabaseHas('seminar_subjects', ['subject' => 'Konteyner Güvenliği', 'status' => 1]);
        $this->assertDatabaseHas('seminar_subject_proposals', ['id' => $proposal->id, 'status' => 'accepted']);
    }

    private function subject(): SeminarSubjects
    {
        return SeminarSubjects::create(['subject' => 'Özgür Yazılım', 'summary' => 'Özet', 'duration' => 2, 'status' => 1]);
    }

    private function data(array $overrides = []): array
    {
        return array_merge(['subject_choice' => 'existing', 'seminar_subject_id' => null, 'summary' => 'Seminer özeti', 'target_audience' => 'Herkes', 'seminar_type' => 'either', 'duration' => 2, 'biography' => 'Örnek özgeçmiş'], $overrides);
    }
}
