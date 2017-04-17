<?php

namespace Ghostscript;

use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;

class LaravelGhostscriptServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {

        $this->publishes([
            __DIR__.'/Config/config.php' => config_path('ghostscript.php'),
        ]);

        $this->mergeConfigFrom(
            __DIR__.'/Config/config.php', 'ghostscript'
        );

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bindShared('ghostscript', function ($app) {

            $configuration = config('ghostscript');

            return Transcoder::create($configuration, $app->make('Psr\Log\LoggerInterface'));

        });

        $this->app->bind('Ghostscript\Transcoder', function($app) {

            return $app['ghostscript'];
            
        });

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('ghostscript');
    }

}