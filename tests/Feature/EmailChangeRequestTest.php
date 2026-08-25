<?php

namespace Tests\Feature;

use App\Mail\EmailChangeRequestProcessed;
use App\Mail\EmailChangeRequestSubmitted;
use App\Models\EmailChangeRequest;
use App\Models\EmailRedirects;
use App\Models\User;
use App\Services\PostfixAdminClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailChangeRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_submit_one_email_change_request_and_management_is_notified(): void
    {
        Mail::fake();
        $user = User::factory()->create(['email' => 'old@example.test']);

        $this->actingAs($user)->post('/email-change-request', [
            'requested_email' => 'NEW@example.test',
            'reason' => 'Eski adresime erişemiyorum.',
            'password' => 'password',
        ])->assertRedirect(route('email-change-requests.create'));

        $this->assertDatabaseHas('email_change_requests', [
            'user_id' => $user->id,
            'current_email' => 'old@example.test',
            'requested_email' => 'new@example.test',
            'status' => 'pending',
        ]);
        Mail::assertSent(EmailChangeRequestSubmitted::class, fn ($mail) => $mail->hasTo('yk@lkd.org.tr'));

        $this->actingAs($user)->post('/email-change-request', [
            'requested_email' => 'another@example.test',
            'password' => 'password',
        ])->assertSessionHas('danger-status');
    }

    public function test_owner_approval_updates_the_member_and_active_postfix_forwarding(): void
    {
        Mail::fake();
        $owner = User::factory()->create(['role' => 1]);
        $user = User::factory()->create(['email' => 'old@example.test']);
        EmailRedirects::create([
            'user_id' => $user->id,
            'email_alias' => 'uye@penguen.org.tr',
            'email_forwarding' => 'old@example.test',
            'status' => 1,
        ]);
        $changeRequest = EmailChangeRequest::create([
            'user_id' => $user->id,
            'current_email' => 'old@example.test',
            'requested_email' => 'new@example.test',
        ]);

        $this->app->instance(PostfixAdminClient::class, new class extends PostfixAdminClient {
            public array $calls = [];
            public function updateAlias(string $aliasEmail, string $targetEmail): bool
            {
                $this->calls[] = [$aliasEmail, $targetEmail];
                return true;
            }
        });

        $this->actingAs($owner)->patch(route('admin.email-change-requests.approve', $changeRequest))
            ->assertSessionHas('success-status');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'email' => 'new@example.test']);
        $this->assertDatabaseHas('email_redirects', ['user_id' => $user->id, 'email_forwarding' => 'new@example.test']);
        $this->assertDatabaseHas('email_change_requests', ['id' => $changeRequest->id, 'status' => 'approved', 'processed_by' => $owner->id]);
        Mail::assertSent(EmailChangeRequestProcessed::class, fn ($mail) => $mail->hasTo('new@example.test'));
    }

    public function test_postfix_failure_keeps_email_change_request_pending(): void
    {
        $owner = User::factory()->create(['role' => 1]);
        $user = User::factory()->create(['email' => 'old@example.test']);
        EmailRedirects::create([
            'user_id' => $user->id,
            'email_alias' => 'uye@penguen.org.tr',
            'email_forwarding' => 'old@example.test',
            'status' => 1,
        ]);
        $changeRequest = EmailChangeRequest::create([
            'user_id' => $user->id,
            'current_email' => 'old@example.test',
            'requested_email' => 'new@example.test',
        ]);

        $this->app->instance(PostfixAdminClient::class, new class extends PostfixAdminClient {
            public function updateAlias(string $aliasEmail, string $targetEmail): bool
            {
                return false;
            }
        });

        $this->actingAs($owner)->patch(route('admin.email-change-requests.approve', $changeRequest))
            ->assertSessionHas('danger-status');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'email' => 'old@example.test']);
        $this->assertDatabaseHas('email_change_requests', ['id' => $changeRequest->id, 'status' => 'pending']);
    }
}
