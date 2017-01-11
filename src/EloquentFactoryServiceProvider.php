<?php

namespace Stidges\EloquentFactory;

use Illuminate\Support\ServiceProvider;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;

class EloquentFactoryServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        if (method_exists($this, 'package')) {
            $this->package('stidges/eloquent-factory', null, __DIR__);
        } else {
            $this->publishes([
                __DIR__.'/config/config.php' => config_path('eloquent-factory.php')
            ]);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if (method_exists($this, 'mergeConfigFrom')) {
            $this->mergeConfigFrom(__DIR__.'/config/config.php', 'eloquent-factory');
        }

        if (!$this->app->bound(FakerGenerator::class)) {
            $this->app->singleton(FakerGenerator::class, function() {
                return FakerFactory::create();
            });
        }

        $this->app->singleton(Factory::class, function($app) {
            $faker = $app->make(FakerGenerator::class);
            $config = $app['config'];
            $path = $config->get('eloquent-factory::factories_path', $config->get('eloquent-factory.factories_path'));

            return Factory::construct($faker, $path);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Factory::class];
    }
}
