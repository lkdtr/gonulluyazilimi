<?php

namespace App\Console\Commands;

use App\Contracts\Messaging\SmsSender;
use App\Support\Organization;
use Illuminate\Console\Command;

class smsTest extends Command
{
    protected $signature = 'sms:test {phone : Alıcı telefon numarası (örn: 905551234567)}';

    protected $description = 'SMS test gönder';

    public function handle(SmsSender $sms, Organization $organization): int
    {
        $phone = $this->argument('phone');
        $code = random_int(100000, 999999);

        $this->info("SMS gönderiliyor → {$phone} (kod: {$code})");
        $sms->send($phone, "{$code} kodu ile telefon numaranızı doğrulayabilirsiniz. ".$organization->name());
        $this->info('Gönderildi.');

        return self::SUCCESS;
    }
}
