<?php

namespace App\Providers;

use App\Support\Organization;
use Illuminate\Support\Facades\View;
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

        $this->app->singleton(Organization::class);
        $this->app->singleton(\App\Support\Agreements::class);

        // Messaging channels behind interfaces; "log" writes instead of sending.
        $this->app->singleton(\App\Contracts\Messaging\SmsSender::class, fn () => config('messaging.sms') === 'log'
            ? new \App\Support\Messaging\LogSender('sms')
            : new \App\Support\Messaging\NetgsmSmsSender());
        $this->app->singleton(\App\Contracts\Messaging\WhatsAppSender::class, fn () => config('messaging.whatsapp') === 'log'
            ? new \App\Support\Messaging\LogSender('whatsapp')
            : new \App\Support\Messaging\WhatsAppBridgeSender());

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

        // Every view reads the association's name, logo and contact details here.
        View::share('organization', $this->app->make(Organization::class));
    }
}
