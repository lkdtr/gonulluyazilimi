<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

use App\Traits\PostfixAdmin;

class aliasTest extends Command
{
    use PostfixAdmin;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alias:test {alias}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Alias Test';

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
        $alias = $this->argument("alias");
        $email_alias = $alias."@penguen.org.tr";

        $result = $this->create_alias($email_alias, "bahri@bahri.info");

        dump($result);
    }
}
