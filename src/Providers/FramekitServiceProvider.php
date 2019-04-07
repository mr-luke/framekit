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
            return new \Framekit\Drivers\EventBus($app);
        });

        $this->app->singleton(\Framekit\Contracts\Store::class, function ($app) {

            return new \Framekit\Drivers\EventStore(
                $app->make(\Framekit\Contracts\Config::class),
                new \Framekit\Eventing\EventSerializer
            );
        });

        $this->app->singleton(\Framekit\Contracts\Projector::class, function ($app) {
            return new \Framekit\Drivers\Projector($app);
        });

        $this->app->singleton(\Framekit\Contracts\EventRepository::class, function ($app) {

            return new \Framekit\Eventing\EventStoreRepository(
                $app->make(\Framekit\Contracts\EventBus::class),
                $app->make(\Framekit\Contracts\Store::class),
                $app->make(\Framekit\Contracts\Projector::class)
            );
        });
    }
}
