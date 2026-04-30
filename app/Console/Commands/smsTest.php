<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use BahriCanli\Netgsm\ShortMessage;
use BahriCanli\Netgsm\NetgsmService;

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
        $phone = $this->argument('phone');
        $code  = rand(100000, 999999);

        $this->info("SMS gönderiliyor → {$phone} (kod: {$code})");

        $message = $code . " kodu ile telefon numaranizi dogrulayin. Linux Kullanicilari Dernegi";

        app('netgsm-sms')->sendOne(
            new ShortMessage($phone, $message)
        );

        $this->info('Gönderildi.');
    }
}
