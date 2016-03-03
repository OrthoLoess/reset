<?php

namespace Reset\Providers;

use Illuminate\Support\ServiceProvider;
use Pheal\Access\StaticCheck;
use Pheal\Cache\PredisStorage;
use Pheal\Core\Config as PhealConfig;
use Pheal\Pheal;
use Reset\Classes\Crest;
use Reset\Classes\EveSSO;

class CrestServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Pheal setup (for XML API)
        PhealConfig::getInstance()->cache = new PredisStorage();
        PhealConfig::getInstance()->access = new StaticCheck();
        PhealConfig::getInstance()->http_user_agent = 'Reset app by Ortho Loess, hosted at '.config('app.url');
        PhealConfig::getInstance()->api_base = config('xml_api.root', "https://api.eveonline.com/");
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Reset\Classes\EveSSO', function() {
            return new EveSSO();
        });
        $this->app->singleton('Reset\Classes\Crest', function($app) {
            return new Crest(false, $app['Reset\Classes\EveSSO']);
        });
        $this->app->singleton('Pheal\Pheal', function() {
            return new Pheal();
        });
    }
}
