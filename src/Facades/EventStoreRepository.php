<?php

namespace Framekit\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @codeCoverageIgnore
 */
class EventStoreRepository extends Facade
{
    /**
     * Create faked EventStoreRepository
     *
     * @return void
     */
    public static function fake(): void
    {
        EventBus::fake();
        EventStore::fake();
        Projector::fake();

        static::swap(
            app()->make('framekit.event.bus'),
            app()->make('framekit.event.store'),
            app()->make('framekit.projector')
        );
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'framekit.event.repository';
    }
}
