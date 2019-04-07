<?php

namespace Framekit\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @codeCoverageIgnore
 */
class EventStoreRepository extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Framekit\Contracts\EventRepository::class;
    }
}
