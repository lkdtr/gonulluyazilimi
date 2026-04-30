<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use NotificationChannels\WhatsAppBridge\WhatsAppFacade as WhatsApp;
use NotificationChannels\WhatsAppBridge\WhatsAppMessage;

class whatsappTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:test {phone : Alıcı telefon numarası (örn: 905551234567)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'WhatsApp bridge üzerinden test mesajı gönder';

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

        $this->info("WhatsApp gönderiliyor → {$phone} (kod: {$code})");

        $message = WhatsAppMessage::create(
            "{$code} kodu ile telefon numaranızı doğrulayabilirsiniz. Linux Kullanıcıları Derneği"
        )->to($phone);

        WhatsApp::sendMessage($message);

        $this->info('Gönderildi.');

        return 0;
    }
}
