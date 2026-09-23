<?php

namespace Tests\Feature;

use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class HttpsUrlTest extends TestCase
{
    protected function tearDown(): void
    {
        URL::forceScheme(null);

        parent::tearDown();
    }

    public function test_redirects_use_https_when_the_site_is_served_over_https(): void
    {
        config(['app.url' => 'https://gonullu.lkd.org.tr']);
        (new AppServiceProvider($this->app))->boot();

        $this->get('http://gonullu.lkd.org.tr/admin', ['X-Forwarded-Proto' => 'https'])
            ->assertRedirect('https://gonullu.lkd.org.tr/login');
    }

    public function test_redirects_keep_http_for_an_http_site(): void
    {
        config(['app.url' => 'http://localhost']);
        (new AppServiceProvider($this->app))->boot();

        $this->get('http://localhost/admin')->assertRedirect('http://localhost/login');
    }
}
