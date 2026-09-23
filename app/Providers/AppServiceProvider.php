<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind('mailgun.client', function() {
            return \Http\Adapter\Guzzle7\Client::createWithConfig([
                // your Guzzle7 configuration
            ]);
        });

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // TLS ends at Cloudflare and the origin is reached over plain HTTP, so the
        // request looks insecure: generate https URLs and redirects whenever the
        // site is served over https.
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}
