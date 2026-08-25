<?php

namespace Tests\Feature;

use App\Mail\SeminarRequestNotification;
use App\Mail\SeminarRequestReceived;
use App\Models\SeminarRequests;
use App\Models\SeminarSubjects;
use App\Models\Organizations;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SeminarRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_catalog_is_available_but_the_request_form_requires_authentication(): void
    {
        $subject = $this->createSubject();

        $this->get('/create-seminar-request')
            ->assertOk()
            ->assertSee($subject->subject)
            ->assertSee('Verilebilecek seminerler')
            ->assertSee('Giriş yaparak talep oluştur');

        $this->get('/create-seminar-request/create')
            ->assertRedirect('/login');
    }

    public function test_iframe_mode_hides_the_site_chrome_and_allows_only_lkd_embedding(): void
    {
        $this->createSubject();

        $this->get('/create-seminar-request?in-iframe=1')
            ->assertOk()
            ->assertDontSee('navbar')
            ->assertSee('create-seminar-offer?in-iframe=1', false)
            ->assertHeader('Content-Security-Policy', "frame-ancestors 'self' https://lkd.org.tr https://www.lkd.org.tr");
    }

    public function test_authenticated_user_sees_subjects_only_in_the_selection_field(): void
    {
        $user = User::factory()->create();
        $subject = $this->createSubject();

        $this->actingAs($user)->get('/create-seminar-request')
            ->assertOk()
            ->assertDontSee('Verilebilecek seminerler')
            ->assertSee('name="seminar_subject_id"', false)
            ->assertSee($subject->subject);
    }

    public function test_seminar_date_must_be_at_least_sixty_days_in_the_future(): void
    {
        $user = User::factory()->create();
        $subject = $this->createSubject();

        $this->actingAs($user)->from('/create-seminar-request')->post('/create-seminar-request', $this->requestData($subject, now()->addDays(59)->toDateString()))
            ->assertRedirect('/create-seminar-request')
            ->assertSessionHasErrors('seminar_start_date');

        $this->assertDatabaseCount('seminar_requests', 0);
    }

    public function test_valid_request_notifies_the_board_and_the_requester(): void
    {
        Mail::fake();
        $user = User::factory()->create(['phone_number' => '905551112233']);
        $subject = $this->createSubject();

        $this->actingAs($user)->post('/create-seminar-request', $this->requestData($subject, now()->addDays(60)->toDateString()))
            ->assertRedirect('/create-seminar-request')
            ->assertSessionHas('success-status');

        $seminarRequest = SeminarRequests::with(['user', 'seminarSubject'])->firstOrFail();
        $this->assertSame('pending', $seminarRequest->status);
        $this->assertSame($user->id, $seminarRequest->user_id);
        $this->assertSame($subject->id, $seminarRequest->seminar_subject_id);
        $this->assertSame('Örnek Üniversite', $seminarRequest->organizationRecord->name);
        $this->assertSame(now()->addDays(60)->toDateString(), $seminarRequest->seminar_start_date->toDateString());
        $this->assertSame(now()->addDays(60)->toDateString(), $seminarRequest->seminar_end_date->toDateString());

        Mail::assertSent(SeminarRequestNotification::class, fn ($mail) => $mail->hasTo('yk@lkd.org.tr'));
        Mail::assertSent(SeminarRequestReceived::class, fn ($mail) => $mail->hasTo($user->email));
    }

    public function test_existing_organization_is_reused_despite_different_letter_case(): void
    {
        Mail::fake();
        $subject = $this->createSubject();
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();

        $this->actingAs($firstUser)->post('/create-seminar-request', $this->requestData($subject, now()->addDays(60)->toDateString()));
        $this->actingAs($secondUser)->post('/create-seminar-request', array_merge(
            $this->requestData($subject, now()->addDays(61)->toDateString()),
            ['organization' => 'ÖRNEK ÜNİVERSİTE']
        ));

        $this->assertDatabaseCount('organizations', 1);
        $this->assertSame(
            SeminarRequests::firstOrFail()->organization_id,
            SeminarRequests::latest('id')->firstOrFail()->organization_id
        );
    }

    public function test_end_date_cannot_be_before_start_date(): void
    {
        $user = User::factory()->create();
        $subject = $this->createSubject();

        $this->actingAs($user)->post('/create-seminar-request', $this->requestData(
            $subject,
            now()->addDays(61)->toDateString(),
            now()->addDays(60)->toDateString()
        ))->assertSessionHasErrors('seminar_end_date');
    }

    public function test_online_seminar_request_does_not_require_an_address(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $subject = $this->createSubject();

        $this->actingAs($user)->post('/create-seminar-request', array_merge(
            $this->requestData($subject, now()->addDays(60)->toDateString()),
            ['seminar_type' => 'online', 'location' => '']
        ))->assertSessionHasNoErrors();

        $this->assertDatabaseHas('seminar_requests', ['seminar_type' => 'online', 'location' => '']);
    }

    public function test_in_person_seminar_request_requires_an_address(): void
    {
        $user = User::factory()->create();
        $subject = $this->createSubject();

        $this->actingAs($user)->post('/create-seminar-request', array_merge(
            $this->requestData($subject, now()->addDays(60)->toDateString()),
            ['seminar_type' => 'in_person', 'location' => '']
        ))->assertSessionHasErrors('location');
    }

    private function createSubject(): SeminarSubjects
    {
        $subject = new SeminarSubjects();
        $subject->subject = 'Özgür Yazılım';
        $subject->summary = 'Özgür yazılıma giriş';
        $subject->duration = 2;
        $subject->status = 1;
        $subject->save();

        return $subject;
    }

    private function requestData(SeminarSubjects $subject, string $startDate, ?string $endDate = null): array
    {
        return [
            'seminar_subject_id' => $subject->id,
            'organization' => 'örnek üniversite',
            'location' => 'Kadıköy, İstanbul',
            'seminar_type' => 'in_person',
            'seminar_start_date' => $startDate,
            'seminar_end_date' => $endDate ?? $startDate,
        ];
    }
}
