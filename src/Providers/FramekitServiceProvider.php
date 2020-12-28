<?php

namespace Framekit\Providers;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Queue\Factory;
use Illuminate\Log\Logger;
use Illuminate\Support\ServiceProvider;
use Mrluke\Bus\Contracts\ProcessRepository;
use Mrluke\Configuration\Host;
use Mrluke\Configuration\Schema;

use Framekit\Contracts\CommandBus;
use Framekit\Contracts\Config;
use Framekit\Contracts\EventRepository;
use Framekit\Contracts\Mapper;
use Framekit\Contracts\Store;
use Framekit\Drivers\EventBus;
use Framekit\Drivers\EventMapper;
use Framekit\Drivers\EventStore;
use Framekit\Drivers\Projector;
use Framekit\Eventing\EventSerializer;
use Framekit\Eventing\EventStoreRepository;
use Framekit\Eventing\Retrospector;

/**
 * ServiceProvider for package.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 *
 * @category  Laravel
 * @package   mr-luke/framekit
 * @licence   MIT
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
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        $this->publishes(
            [__DIR__ . '/../../config/framekit.php' => config_path('framekit.php')],
            'config'
        );

        $this->publishes(
            [__DIR__ . '/../../database/migrations/' => database_path('migrations')],
            'migrations'
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/framekit.php', 'framekit');

        $this->app->bind(
            CommandBus::class,
            function($app) {
                return $app->make(\Mrluke\Bus\Contracts\CommandBus::class);
            }
        );

        $this->app->singleton(
            Config::class,
            function($app) {

                $schema = Schema::createFromFile(
                    __DIR__ . '/../../config/schema.json',
                    true
                );

                return new Host(
                    $app['config']->get('framekit'),
                    $schema
                );
            }
        );

        $this->app->singleton(
            \Framekit\Contracts\EventBus::class,
            function($app) {
                return $app->make('framekit.event.bus');
            }
        );

        $this->app->singleton(
            Mapper::class,
            function($app) {
                return $app->make('framekit.event.mapper');
            }
        );

        $this->app->bind(
            \Framekit\Contracts\Projector::class,
            function($app) {
                return $app->make('framekit.projector');
            }
        );

        $this->app->bind(
            Store::class,
            function($app) {
                return $app->make('framekit.event.store');
            }
        );

        $this->app->bind(
            EventRepository::class,
            function($app) {
                return $app->make('framekit.event.repository');
            }
        );

        $this->app->bind(
            \Framekit\Contracts\Retrospector::class,
            function($app) {
                return $app->make('framekit.event.retrospector');
            }
        );

        $this->app->singleton(
            'framekit.projector',
            function($app) {
                return new Projector($app);
            }
        );

        $this->app->singleton(
            'framekit.event.bus',
            function($app) {
                /* @var \Illuminate\Foundation\Application $app */
                $container = $app->make(Container::class);

                return new EventBus(
                    $app->make(Config::class),
                    $app->make(ProcessRepository::class),
                    $container,
                    $app->make(Logger::class),
                    function($connection = null) use ($app) {
                        return $app->make(Factory::class)->connection($connection);
                    }
                );
            }
        );

        $this->app->singleton(
            'framekit.event.mapper',
            function($app) {
                return new EventMapper($app);
            }
        );

        $this->app->singleton(
            'framekit.event.store',
            function($app) {

                return new EventStore(
                    $app->make(Config::class),
                    new EventSerializer,
                    $app->make(Mapper::class)
                );
            }
        );

        $this->app->singleton(
            'framekit.event.repository',
            function($app) {

                return new EventStoreRepository(
                    $app->make('framekit.event.bus'),
                    $app->make('framekit.event.store'),
                    $app->make('framekit.projector')
                );
            }
        );

        $this->app->singleton(
            'framekit.event.retrospector',
            function($app) {

                return new Retrospector(
                    $app->make('framekit.event.bus'),
                    $app->make('framekit.event.store'),
                    $app->make('framekit.projector')
                );
            }
        );
    }
}
