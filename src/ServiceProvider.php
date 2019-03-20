<?php

namespace Mrluke\Framekit;

use Illuminate\Support\ServiceProvider as Provider;

/**
 * ServiceProvider for package.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 *
 * @category  Laravel
 * @package   mr-luke/framekit
 * @license   MIT
 */
class ServiceProvider extends Provider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([__DIR__ .'/../config/framekit.php' => config_path('framekit.php')], 'config');

        $this->publishes([__DIR__.'/../database/migrations/' => database_path('migrations')], 'migrations');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ .'/../config/framekit.php', 'framekit');

        $this->app->singleton(\Mrluke\Framekit\Contracts\Config::class, function ($app) {

            $schema = \Mrluke\Configuration\Schema::createFromFile(
                __DIR__.'/../config/schema.json',
                true
            );

            return new \Mrluke\Configuration\Host(
                $app['config']->get('framekit'),
                $schema
            );
        });

        $this->app->singleton(\Mrluke\Framekit\Contracts\Snapshot::class, function ($app) {

            return new \Mrluke\Framekit\Eventing\Snapshot(
                $app->make(\Mrluke\Framekit\Contracts\Config::class)
            );
        });

        $this->app->singleton(\Mrluke\Framekit\Contracts\Stream::class, function ($app) {

            return new \Mrluke\Framekit\Eventing\EventStream(
                $app->make(\Mrluke\Framekit\Contracts\Config::class)
            );
        });

        $this->app->singleton(\Mrluke\Framekit\Contracts\Store::class, function ($app) {

            return new \Mrluke\Framekit\Eventing\EventStore(
                $app->make(\Mrluke\Framekit\Contracts\Config::class),
                $app->make(\Mrluke\Framekit\Contracts\Stream::class),
                $app->make(\Mrluke\Framekit\Contracts\Snapshot::class)
            );
        });

        $this->app->singleton(\Mrluke\Framekit\Contracts\Handler::class, \Mrluke\Framekit\Eventing\EventHandler::class);
    }
}
