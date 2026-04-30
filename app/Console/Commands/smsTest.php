<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Notification;

use App\Notifications\MobileVerification;

class smsTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:test {phone : Alıcı telefon numarası (örn: 905551234567)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'SMS test gönder';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $smsObject = new \stdClass();
        $smsObject->phone_number = $this->argument('phone');
        $smsObject->verification_code = rand(100000, 999999);

        $this->info("SMS gönderiliyor → {$smsObject->phone_number} (kod: {$smsObject->verification_code})");

        Notification::send($smsObject, new MobileVerification());

        $this->info('Gönderildi.');
    }
}
