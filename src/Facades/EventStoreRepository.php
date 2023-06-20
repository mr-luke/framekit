<?php

namespace Framekit\Facades;

use Illuminate\Support\Facades\Facade;
use Framekit\Eventing\EventStoreRepository as Faked;

/**
 * @codeCoverageIgnore
 */
class EventStoreRepository extends Facade
{
    /**
     * Create faked EventStoreRepository
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function fake(): void
    {
        EventBus::fake();
        EventStore::fake();
        Projector::fake();

        static::swap(new Faked(
            app()->make('framekit.event.bus'),
            app()->make('framekit.event.store'),
            app()->make('framekit.projector')
        ));
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'framekit.event.repository';
    }
}
