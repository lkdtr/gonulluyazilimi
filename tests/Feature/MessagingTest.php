<?php

namespace Tests\Feature;

use App\Contracts\Messaging\SmsSender;
use App\Contracts\Messaging\WhatsAppSender;
use App\Models\User;
use App\Support\Consents;
use App\Support\Messaging\Messenger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessagingTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_verification_code_goes_through_the_configured_senders(): void
    {
        $this->post('/phone-number-verification-request', ['phone_number' => '905551112233'])->assertOk()->assertJson(['status' => true]);

        $sms = app(SmsSender::class)->sent;
        $whatsApp = app(WhatsAppSender::class)->sent;
        $this->assertCount(1, $sms);
        $this->assertSame('905551112233', $sms[0]['phone']);
        $this->assertMatchesRegularExpression('/^\d{6} kodu ile telefon numaranızı doğrulayabilirsiniz\./u', $sms[0]['text']);
        $this->assertCount(1, $whatsApp);
    }

    public function test_informational_messages_follow_consents(): void
    {
        $user = User::factory()->create(['phone_number' => '905551112233']);
        app(Consents::class)->set($user->contact, ['sms' => true, 'whatsapp' => false], 'profile');

        $sent = app(Messenger::class)->inform($user->contact, 'Duyuru', 'Toplantı cuma günü.', ['email', 'sms', 'whatsapp']);

        $this->assertSame(['sms'], $sent);
        $this->assertSame('Toplantı cuma günü.', app(SmsSender::class)->sent[0]['text']);
        $this->assertSame([], app(WhatsAppSender::class)->sent);
        $this->assertCount(0, app('mailer')->getSymfonyTransport()->messages());
    }
}
