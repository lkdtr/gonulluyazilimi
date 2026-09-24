<?php

namespace Tests\Feature;

use App\Mail\Welcome;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class QueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_emails_wait_in_the_database_queue_until_the_worker_sends_them(): void
    {
        config(['queue.default' => 'database']);
        $user = User::factory()->create(['email' => 'ada@example.org']);

        Mail::to($user->email)->send(new Welcome($user));

        $this->assertSame(1, DB::table('jobs')->count());
        $this->assertCount(0, app('mailer')->getSymfonyTransport()->messages());

        $this->artisan('queue:work', ['connection' => 'database', '--once' => true, '--stop-when-empty' => true])->assertSuccessful();

        $this->assertSame(0, DB::table('jobs')->count());
        $this->assertCount(1, app('mailer')->getSymfonyTransport()->messages());
    }
}
