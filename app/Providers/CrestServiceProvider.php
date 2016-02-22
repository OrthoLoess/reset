<?php

namespace Reset\Providers;

use Illuminate\Support\ServiceProvider;
use Reset\Classes\Crest;

class CrestServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Reset\Classes\Crest', function() {
            return new Crest();
        });
    }
}
