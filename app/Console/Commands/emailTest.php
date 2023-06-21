<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Mail;

use App\Mail\Welcome;
use App\Mail\PenguenWelcome;

class emailTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email Test';

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
        $reveiverEmailAddress = "bahri@bahri.info";
        $data = new \stdClass();
        $data->name = "Bahri";
        $data->surname = "Canlı";
        $data->email = "bahri@bahri.info";

        Mail::to($reveiverEmailAddress)->send(new Welcome($data));
        dump( Mail::failures()  );

        $reveiverEmailAddress = "bahri.canli@penguen.org.tr";
        $data = new \stdClass();
        $data->name = "Bahri";
        $data->surname = "Canlı";
        $data->email = "bahri@bahri.info";
        $data->alias = "bahri.canli@penguen.org.tr";

        Mail::to($reveiverEmailAddress)->send(new PenguenWelcome($data));
        dump( Mail::failures()  );

    }
}
