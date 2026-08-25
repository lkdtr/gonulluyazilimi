<?php

namespace Tests\Feature;

use App\Models\ContactPermissions;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PhoneVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_code_requests_store_only_a_hashed_expiring_code(): void
    {
        Notification::fake();

        $this->postJson('/phone-number-verification-request', [
            'phone_number' => '905551112233',
        ])->assertOk()->assertJson(['status' => true]);

        $verification = ContactPermissions::where('value', '905551112233')->firstOrFail();
        $this->assertStringStartsWith('$2y$', $verification->verification_code);
        $this->assertTrue($verification->verification_code_expires_at->isFuture());
        $this->assertFalse($verification->verified);
    }

    public function test_phone_verification_marks_a_valid_unexpired_code_as_verified(): void
    {
        $verification = ContactPermissions::create([
            'value_type' => 'phone_number',
            'value' => '905551112233',
            'verification_code' => Hash::make('123456'),
            'verification_code_expires_at' => now()->addMinutes(10),
            'verification_attempts' => 0,
            'verified' => false,
            'status' => 1,
        ]);

        $this->postJson('/phone-number-verification', [
            'phone_number' => '905551112233',
            'validation' => '123456',
        ])->assertOk()->assertJson(['status' => true]);

        $verification->refresh();
        $this->assertTrue($verification->verified);
        $this->assertNotNull($verification->verified_at);
        $this->assertNull($verification->verification_code);
    }

    public function test_expired_or_invalid_codes_do_not_verify_the_phone(): void
    {
        ContactPermissions::create([
            'value_type' => 'phone_number',
            'value' => '905551112233',
            'verification_code' => Hash::make('123456'),
            'verification_code_expires_at' => now()->subMinute(),
            'verified' => false,
            'status' => 1,
        ]);

        $this->postJson('/phone-number-verification', [
            'phone_number' => '905551112233',
            'validation' => '123456',
        ])->assertOk()->assertJson(['status' => false, 'message' => 'Code expired']);

        $this->assertDatabaseHas('contact_permissions', [
            'value' => '905551112233',
            'verified' => false,
        ]);
    }

    public function test_registration_requires_a_recent_phone_verification(): void
    {
        Mail::fake();

        $this->from('/register')->post('/register', $this->registrationData())
            ->assertRedirect('/register')
            ->assertSessionHasErrors('phone_number');

        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_records_the_verified_phone_timestamp(): void
    {
        Mail::fake();
        $verifiedAt = now()->subMinute();
        ContactPermissions::create([
            'value_type' => 'phone_number',
            'value' => '905551112233',
            'verified' => true,
            'verified_at' => $verifiedAt,
            'status' => 1,
        ]);

        $this->post('/register', $this->registrationData())
            ->assertRedirect('/home');

        $user = User::where('email', 'ayse@example.test')->firstOrFail();
        $this->assertSame(
            $verifiedAt->format('Y-m-d H:i:s'),
            $user->phone_number_verified_at->format('Y-m-d H:i:s')
        );
    }

    private function registrationData(): array
    {
        return [
            'name' => 'Ayşe',
            'surname' => 'Yılmaz',
            'email' => 'ayse@example.test',
            'phone_number' => '905551112233',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agreement' => '1',
        ];
    }
}
