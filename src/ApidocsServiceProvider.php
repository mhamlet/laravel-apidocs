<?php

namespace MHamlet\Apidocs;

use Illuminate\Support\ServiceProvider;
use phpDocumentor\Reflection\DocBlock\Tag;

class ApidocsServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot() {

        // Registering custom tags
        Tag::registerTagHandler('apiParam', '\phpDocumentor\Reflection\DocBlock\Tag\ParamTag');

        // Set router
        RouteResolver::setRouter(app()->make('router'));

        // use this if your package needs a config file
        // $this->publishes([
        //         __DIR__.'/config/config.php' => config_path('skeleton.php'),
        // ]);

        // use the vendor configuration file as fallback
        // $this->mergeConfigFrom(
        //     __DIR__.'/config/config.php', 'skeleton'
        // );
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {

//        $this->app['apidocs.generate'] = $this->app->share(function ($app) {
//
//            return $this->app->make('mhamlet\Apidocs\Commands\ApiDocsGeneratorCommand');
//        });
//        $this->commands('apidocs.generate');
    }
}