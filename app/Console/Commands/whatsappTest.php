<?php

namespace App\Console\Commands;

use App\Contracts\Messaging\WhatsAppSender;
use App\Support\Organization;
use Illuminate\Console\Command;

class whatsappTest extends Command
{
    protected $signature = 'whatsapp:test {phone : Alıcı telefon numarası (örn: 905551234567)}';

    protected $description = 'WhatsApp bridge üzerinden test mesajı gönder';

    public function handle(WhatsAppSender $whatsApp, Organization $organization): int
    {
        if (! $whatsApp->available()) {
            $this->error('WhatsApp bridge tanımlı değil (WHATSAPP_BRIDGE_URL).');

            return self::FAILURE;
        }

        $phone = $this->argument('phone');
        $code = random_int(100000, 999999);

        $this->info("WhatsApp gönderiliyor → {$phone} (kod: {$code})");
        $whatsApp->send($phone, "{$code} kodu ile telefon numaranızı doğrulayabilirsiniz. ".$organization->name());
        $this->info('Gönderildi.');

        return self::SUCCESS;
    }
}
