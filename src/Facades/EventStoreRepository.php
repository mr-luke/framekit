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
        EventStore::fake();
        Projector::fake();
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
