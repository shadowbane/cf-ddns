<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Log on PHAR build
        // ensure you configure the right channel you use
        config([
            'logging.channels.single.path' => \Phar::running()
                ? '/var/log/cfddns.log'
                : storage_path('logs/cfddns.log'),
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (app()->environment('development')) {
            app()->register(\Intonate\TinkerZero\TinkerZeroServiceProvider::class);
        }

        app()->singleton(\App\CloudflareService\Connector::class);
        app()->singleton(\App\CloudflareService\Zone::class);
        app()->singleton(\App\CloudflareService\DNS::class);
    }
}
