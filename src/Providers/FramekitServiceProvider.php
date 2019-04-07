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

        // $this->app->singleton(\Framekit\Contracts\Serializer::class, function ($app) {
        //
        //     return new \Framekit\Eventing\Serializer(
        //         $app->make(\Framekit\Contracts\Config::class)
        //     );
        // });
        //
        // $this->app->singleton(\Framekit\Contracts\Snapshot::class, function ($app) {
        //
        //     $config = $app->make(\Framekit\Contracts\Config::class);
        //     $class  = $config->get('drivers.snapshot');
        //
        //     return new $class($config);
        // });
        //
        // $this->app->singleton(\Framekit\Contracts\Stream::class, function ($app) {
        //
        //     $config = $app->make(\Framekit\Contracts\Config::class);
        //     $class  = $config->get('drivers.stream');
        //
        //     return new $class($config);
        // });
        //
        // $this->app->singleton(\Framekit\Contracts\Store::class, function ($app) {
        //
        //     return new \Framekit\Eventing\EventStore(
        //         $app->make(\Framekit\Contracts\Stream::class),
        //         $app->make(\Framekit\Contracts\Snapshot::class)
        //     );
        // });
        //
        // $this->app->singleton(\Framekit\Contracts\Bus::class, function ($app) {
        //
        //     return new \Framekit\Eventing\EventBus([
        //         \Framekit\Base\AggregateCreated::class => \Framekit\Base\CreationReducer::class,
        //         \Framekit\Base\AggregateRemoved::class => \Framekit\Base\RemovalReducer::class
        //     ]);
        // });
    }
}
