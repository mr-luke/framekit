<?php

namespace Framekit\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * ServiceProvider for package.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 *
 * @category  Laravel
 * @package   mr-luke/framekit
 * @license   MIT
 *
 * @codeCoverageIgnore
 */
class FramekitServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        $this->publishes([__DIR__ .'/../../config/framekit.php' => config_path('framekit.php')], 'config');

        $this->publishes([__DIR__.'/../../database/migrations/' => database_path('migrations')], 'migrations');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ .'/../../config/framekit.php', 'framekit');

        $this->app->singleton(\Framekit\Contracts\Config::class, function ($app) {

            $schema = \Mrluke\Configuration\Schema::createFromFile(
                __DIR__.'/../../config/schema.json',
                true
            );

            return new \Mrluke\Configuration\Host(
                $app['config']->get('framekit'),
                $schema
            );
        });

        $this->app->singleton(\Framekit\Contracts\CommandBus::class, function ($app) {
            return new \Framekit\Drivers\CommandBus($app);
        });

        $this->app->singleton(\Framekit\Contracts\EventBus::class, function ($app) {
            return $app->make('framekit.event.bus');
        });

        $this->app->bind(\Framekit\Contracts\Projector::class, function ($app) {
            return $app->make('framekit.projector');
        });

        $this->app->bind(\Framekit\Contracts\Store::class, function ($app) {
            return $app->make('framekit.event.store');
        });

        $this->app->bind(\Framekit\Contracts\EventRepository::class, function ($app) {
            return $app->make('framekit.event.repository');
        });

        $this->app->singleton('framekit.projector', function ($app) {
            return new \Framekit\Drivers\Projector($app);
        });

        $this->app->singleton('framekit.event.bus', function ($app) {
            return new \Framekit\Drivers\EventBus($app);
        });

        $this->app->singleton('framekit.event.store', function ($app) {

            return new \Framekit\Drivers\EventStore(
                $app->make(\Framekit\Contracts\Config::class),
                new \Framekit\Eventing\EventSerializer
            );
        });

        $this->app->singleton('framekit.event.repository', function ($app) {

            return new \Framekit\Eventing\EventStoreRepository(
                $app->make('framekit.event.bus'),
                $app->make('framekit.event.store'),
                $app->make('framekit.projector')
            );
        });
    }
}
