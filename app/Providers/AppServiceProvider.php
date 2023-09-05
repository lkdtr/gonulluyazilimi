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
        if($this->checkHTTPSStatus()){
            URL::forceScheme('https');
        }
    }

    private function checkHTTPSStatus()
    {
	    return (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO']==='http');
    }
}
